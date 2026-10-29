<?php

declare(strict_types=1);

namespace Nitsan\NsNewsComments\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2024
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use GeorgRinger\News\Domain\Repository\NewsRepository;
use Nitsan\NsNewsComments\Domain\Model\Comment;
use Nitsan\NsNewsComments\Domain\Repository\CommentRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Crypto\HashService;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * CommentController
 */
class CommentController extends ActionController
{
    /**
     * commentRepository
     */
    protected ?CommentRepository $commentRepository = null;

    /**
     * @var NewsRepository
     */
    protected NewsRepository $newsRepository;

    /**
     * @var PersistenceManager
     */
    protected PersistenceManager $persistenceManager;

    protected Context $context;

    protected int $newsUid;

    protected int $pageUid;

    /**
     * @var array
     */
    protected array $typo3VersionArray = [];

    private const STORAGE_PID_HMAC_SECRET = 'ns_news_comments_storagePid';

    private const APPROVAL_HMAC_SECRET = 'ns_news_comments_approveComment';

    private const CAPTCHA_SESSION_NAME = 'ns_news_comments_captcha';

    private const CAPTCHA_SESSION_KEY = 'ns_news_comments_captcha_code';

    public function __construct(
        CommentRepository $commentRepository,
        PersistenceManager $persistenceManager,
        NewsRepository $newsRepository,
        Context $context
    ) {
        $this->commentRepository = $commentRepository;
        $this->persistenceManager = $persistenceManager;
        $this->newsRepository = $newsRepository;
        $this->context = $context;
    }

    /**
     * action initialize
     *
     * @return void
     */
    public function initializeAction(): void
    {
        $this->typo3VersionArray = VersionNumberUtility::convertVersionStringToArray(
            VersionNumberUtility::getCurrentTypo3Version()
        );
        $getData = $this->request->getQueryParams();
        $postData = $this->request->getParsedBody();
        $requestData = array_merge($getData, (array)$postData);
        $newsArr = $requestData['tx_news_pi1'] ?? [];
        $pluginArgs = $requestData['tx_nsnewscomments_newscomment'] ?? [];
        $postedComment = is_array($pluginArgs['newComment'] ?? null) ? $pluginArgs['newComment'] : [];
        $this->newsUid = (int)($newsArr['news'] ?? $postedComment['newsuid'] ?? $pluginArgs['newsuid'] ?? 0);

        if ($this->typo3VersionArray['version_main'] <= 12) {
            // @extensionScannerIgnoreLine
            $this->pageUid = (int)$GLOBALS['TSFE']->id;
        } else {
            $this->pageUid = $GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.page.information')->getId();
        }
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );

        if (empty($extbaseFrameworkConfiguration['persistence']['storagePid'])) {
            $pluginArgs = $requestData['tx_nsnewscomments_newscomment'] ?? [];
            $requestedPid = trim((string)($pluginArgs['Storagepid'] ?? ''));
            $requestedHmac = (string)($pluginArgs['storagePidHmac'] ?? '');
            if ($this->isTrustedStoragePid($requestedPid, $requestedHmac)) {
                $currentPid['persistence']['storagePid'] = $requestedPid;
            } elseif (!empty($this->settings['storagePid'])) {
                $currentPid['persistence']['storagePid'] = $this->settings['storagePid'];
            } else {
                $currentPid['persistence']['storagePid'] = $this->pageUid;
            }
            $this->configurationManager->setConfiguration(
                array_merge($extbaseFrameworkConfiguration, $currentPid)
            );
        }
    }

    /**
     * action list
     *
     * @return ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );

        if (empty($extbaseFrameworkConfiguration['persistence']['storagePid'])) {
            $pid = $this->pageUid;
        } else {
            $pid = $extbaseFrameworkConfiguration['persistence']['storagePid'];
        }
        $setting = $this->settings;

        if ($this->newsUid) {
            $comments = $this->commentRepository->getCommentsByNews($this->newsUid)->toArray();

            if (isset($this->settings['captcha']) && ($this->settings['captcha'] == '0' || $this->settings['captcha'] == '')) {
                $paths = $this->captchaVerificationPath();
                $captcha_path = $paths['captcha'] . '?' . rand();
                $this->view->assignMultiple([
                    'captcha_path' => $captcha_path,
                    'verification' => $paths['verification'],
                ]);
            }

            if ((string)($setting['global'] ?? '') === '1') {
                $setting = array_merge($setting, $this->getGlobalDateTimeFormats());
            }

            $approvalFlag = $this->isCommentApprovalRequired() ? '1' : '0';
            $this->view->assignMultiple([
                'comments' => $comments,
                'newsID' => $this->newsUid,
                'pageid' => $this->pageUid,
                'pid' => $pid,
                'pidHmac' => $this->hmacStoragePid((string)$pid),
                'approvalFlag' => $approvalFlag,
                'approvalHmac' => $this->hmacApprovalFlag($approvalFlag),
                'settings' => $setting,
            ]);
        } else {
            $error = LocalizationUtility::translate('tx_nsnewscomments_domain_model_comment.errorMessage', 'NsNewsComments');
            $this->addFlashMessage($error, '', ContextualFeedbackSeverity::ERROR);
        }
        return $this->htmlResponse();
    }

    /**
     * action create
     *
     * @param Comment $newComment
     *
     * @return ResponseInterface
     */
    public function createAction(Comment $newComment): ResponseInterface
    {
        $arguments = $this->request->getArguments();
        $captchaCode = (string)($arguments['captcha_code'] ?? '');
        if ($this->isCaptchaRequired() || $captchaCode !== '') {
            if (!$this->isValidCaptcha($captchaCode)) {
                return $this->jsonResponse(json_encode(['error' => 'captcha']))->withStatus(400);
            }
        }

        if ($this->isTermsAcceptanceRequired() && !$newComment->getTerms()) {
            return $this->jsonResponse(json_encode([
                'error' => 'validation',
                'fields' => ['terms'],
            ]))->withStatus(400);
        }

        $postedComment = $arguments['newComment'] ?? [];
        $username = $this->trimCommentField((string)($postedComment['username'] ?? $newComment->getUsername()));
        $usermail = $this->trimCommentField((string)($postedComment['usermail'] ?? $newComment->getUsermail()));
        $description = $this->trimCommentField((string)($postedComment['description'] ?? $newComment->getDescription()));
        $fields = [];
        if ($username === '') {
            $fields[] = 'username';
        }
        if ($usermail === '') {
            $fields[] = 'usermail';
        }
        if ($this->isBlankCommentText($description)) {
            $fields[] = 'description';
        }
        if ($fields !== []) {
            return $this->jsonResponse(json_encode([
                'error' => 'validation',
                'fields' => $fields,
            ]))->withStatus(400);
        }
        $newComment->setUsername($username);
        $newComment->setUsermail($usermail);
        $newComment->setDescription($description);

        if ($this->newsUid < 1) {
            $this->newsUid = (int)$newComment->getNewsuid();
        }
        if ($this->newsUid < 1) {
            return $this->jsonResponse(json_encode(['error' => 'news']))->withStatus(400);
        }

        $newComment->setNewsuid($this->newsUid);
        $newComment->setPageid($this->pageUid);
        $newComment->setCrdate(time());
        $language = $this->context->getPropertyFromAspect('language', 'id');
        $newComment->setSysLanguageUid($language);

        $parentId = (int)($arguments['parentId'] ?? 0);
        if ($parentId > 0) {
            $parentComment = $this->commentRepository->findByUid($parentId);
            if ($parentComment === null || $parentComment->getNewsuid() !== $this->newsUid) {
                return $this->jsonResponse(json_encode(['error' => 'parent']))->withStatus(400);
            }
            $parentComment->addChildcomment($newComment);
            $this->commentRepository->update($parentComment);
        }

        $needsApproval = $this->resolveCommentApprovalRequired($arguments);
        if ($needsApproval) {
            $newComment->setHidden(1);
        }

        $this->commentRepository->add($newComment);
        $this->persistenceManager->persistAll();
        $news = $this->newsRepository->findByUid($newComment->getNewsuid());

        $paramLink = $this->buildUriByUid(
            $this->pageUid,
            $news,
            ['commentid' => $newComment->getUid()]
        );
        $newComment->setParamlink($paramLink);
        $this->commentRepository->update($newComment);

        $this->persistenceManager->persistAll();
        if ($needsApproval) {
            return $this->jsonResponse(json_encode(['status' => 'pending']));
        }

        $json[$newComment->getUid()] = ['parentId' => $parentId, 'comment' => 'comment'];
        return $this->jsonResponse(json_encode($json));
    }

    /**
     * Site settings are used when the site defines them (Site Set or settings.yaml), otherwise
     * TypoScript, e.g. on TYPO3 v12. TypoScript is read directly because FlexForm values
     * override the plugin settings.
     *
     * @return array{dateFormat: string, timeFormat: string}
     */
    private function getGlobalDateTimeFormats(): array
    {
        $site = $this->request->getAttribute('site');
        $siteSettings = $site instanceof Site ? $site->getSettings() : null;
        $typoScript = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $typoScriptSettings = $typoScript['plugin.']['tx_nsnewscomments.']['settings.'] ?? [];

        $formats = [];
        foreach (['dateFormat' => 'F j, Y', 'timeFormat' => 'g:i a'] as $format => $default) {
            $siteSetting = 'nsNewsComments.settings.' . $format;
            $value = $siteSettings?->has($siteSetting)
                ? $siteSettings->get($siteSetting)
                : ($typoScriptSettings[$format] ?? '');
            $formats[$format] = trim(is_scalar($value) ? (string)$value : '') ?: $default;
        }

        return $formats;
    }

    /**
     * Returns a built URI by pageUid
     *
     * @param int $uid The uid to use for building link
     * @param $news
     * @param array $arguments
     * @return string The link
     */
    private function buildUriByUid(int $uid, $news, array $arguments = []): string
    {
        $commentId = $arguments['commentid'];
        $excludeFromQueryString = [
            'tx_nsnewscomments_newscomment[action]',
            'tx_nsnewscomments_newscomment[controller]',
            'tx_nsnewscomments_newscomment',
            'type'
        ];
        $this->uriBuilder
            ->reset()
            ->setTargetPageUid($uid)
            ->setAddQueryString(true)
            ->setArgumentsToBeExcludedFromQueryString($excludeFromQueryString)
            ->setSection('comments-' . $commentId);

        if (array_key_exists('formail', $arguments)) {
            $this->uriBuilder->setArguments(['frommail' => 1]);
        }

        $uri = $this->uriBuilder->uriFor('detail', ['news' => $news], 'News', 'News', 'Pi1');
        return $this->addBaseUriIfNecessary($uri);
    }

    /**
     * getPath for composer based setup
     * @param $path
     * @param $extName
     * @return string
     */
    public function getPath($path, $extName): string
    {
        $publicPath = sprintf('EXT:%s/Resources/Public/%s', $extName, ltrim((string)$path, '/'));
        $url = $this->publicResourceUrl($publicPath);
        $pathPart = parse_url($url, PHP_URL_PATH);

        return ltrim((string)($pathPart ?? $url), '/');
    }

    /**
     * @return array{captcha: string, verification: string}
     */
    private function captchaVerificationPath(): array
    {
        if (class_exists(\TYPO3\CMS\Core\SystemResource\SystemResourceFactory::class)) {
            return [
                'captcha' => $this->publicResourceUrl('EXT:ns_news_comments/Resources/Public/PHP/captcha.php'),
                'verification' => $this->publicResourceUrl('EXT:ns_news_comments/Resources/Public/PHP/verify.php'),
            ];
        }
        if (Environment::isComposerMode()) {
            $assetPath = $this->getPath('PHP/', 'ns_news_comments');
            $normalizedParams = $this->request->getAttribute('normalizedParams');
            $basePath = $normalizedParams instanceof NormalizedParams
                ? $normalizedParams->getSiteUrl()
                : '';
            return [
                'captcha' => $basePath . $assetPath . 'captcha.php',
                'verification' => $basePath . $assetPath . 'verify.php',
            ];
        }
        $basePath = PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath('ns_news_comments'));
        return [
            'captcha' => '/' . $basePath . 'Resources/Public/PHP/captcha.php',
            'verification' => '/' . $basePath . 'Resources/Public/PHP/verify.php',
        ];
    }

    private function publicResourceUrl(string $extPath): string
    {
        if (class_exists(\TYPO3\CMS\Core\SystemResource\SystemResourceFactory::class)) {
            $factory = GeneralUtility::makeInstance(\TYPO3\CMS\Core\SystemResource\SystemResourceFactory::class);
            $publisher = GeneralUtility::makeInstance(
                \TYPO3\CMS\Core\SystemResource\Publishing\SystemResourcePublisherInterface::class
            );
            $resource = $factory->createPublicResource($extPath);
            $options = new \TYPO3\CMS\Core\SystemResource\Publishing\UriGenerationOptions(
                absoluteUri: true,
                cacheBusting: false
            );

            return (string)$publisher->generateUri($resource, $this->request, $options);
        }

        // @extensionScannerIgnoreLine
        return PathUtility::getPublicResourceWebPath($extPath);
    }

    private function isCaptchaRequired(): bool
    {
        $disableCaptcha = $this->settings['captcha'] ?? '1';

        return $disableCaptcha === '0' || $disableCaptcha === '' || $disableCaptcha === 0 || $disableCaptcha === false;
    }

    private function isTermsAcceptanceRequired(): bool
    {
        $termsRequired = $this->settings['termsRequired'] ?? 0;

        return $termsRequired === 1 || $termsRequired === '1' || $termsRequired === true;
    }

    private function isCommentApprovalRequired(): bool
    {
        $approveComment = $this->settings['approveComment'] ?? 0;

        return $approveComment === 1 || $approveComment === '1' || $approveComment === true;
    }

    /**
     * AJAX create runs as a PAGE USER, so FlexForm from the plugin is not in $this->settings.
     * listAction signs the resolved flag; a matching hmac is the plugin value, otherwise TypoScript.
     */
    private function resolveCommentApprovalRequired(array $arguments): bool
    {
        $posted = ((string)($arguments['approval'] ?? '')) === '1' ? '1' : '0';
        $hmac = (string)($arguments['approvalHmac'] ?? '');
        if ($hmac === '') {
            return $this->isCommentApprovalRequired();
        }
        if (!$this->isTrustedApprovalFlag($posted, $hmac)) {
            return true;
        }

        return $posted === '1';
    }

    private function isValidCaptcha(string $submitted): bool
    {
        $submitted = trim($submitted);
        if ($submitted === '') {
            return false;
        }

        $expected = $this->withCaptchaSession(static function (array &$session): string {
            return (string)($session[self::CAPTCHA_SESSION_KEY] ?? '');
        });

        if ($expected === '' || !hash_equals($expected, $submitted)) {
            return false;
        }

        $this->withCaptchaSession(static function (array &$session): string {
            unset($session[self::CAPTCHA_SESSION_KEY]);
            return '';
        });

        return true;
    }

    /**
     * @param callable(array<string, mixed>): string $callback
     */
    private function withCaptchaSession(callable $callback): string
    {
        $cookieId = (string)($this->request->getCookieParams()[self::CAPTCHA_SESSION_NAME] ?? '');
        if ($cookieId === '') {
            return '';
        }

        $previousName = session_name();
        $previousId = session_id();
        $wasActive = session_status() === PHP_SESSION_ACTIVE;
        if ($wasActive) {
            session_write_close();
        }

        session_name(self::CAPTCHA_SESSION_NAME);
        session_id($cookieId);
        session_start();
        $result = $callback($_SESSION);
        session_write_close();

        session_name($previousName);
        if ($wasActive && $previousId !== '') {
            session_id($previousId);
            session_start();
        }

        return $result;
    }

    private function trimCommentField(string $value): string
    {
        return preg_replace('/^\s+|\s+$/u', '', $value) ?? '';
    }

    private function isBlankCommentText(string $value): bool
    {
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('/\s+/u', '', $value) ?? '';

        return $value === '';
    }

    private function hmacStoragePid(string $pid): string
    {
        if ($pid === '') {
            return '';
        }
        if (class_exists(HashService::class)) {
            return GeneralUtility::makeInstance(HashService::class)->hmac($pid, self::STORAGE_PID_HMAC_SECRET);
        }

        // @extensionScannerIgnoreLine
        return GeneralUtility::hmac($pid, self::STORAGE_PID_HMAC_SECRET);
    }

    private function hmacApprovalFlag(string $flag): string
    {
        $flag = $flag === '1' ? '1' : '0';
        if (class_exists(HashService::class)) {
            return GeneralUtility::makeInstance(HashService::class)->hmac($flag, self::APPROVAL_HMAC_SECRET);
        }

        // @extensionScannerIgnoreLine
        return GeneralUtility::hmac($flag, self::APPROVAL_HMAC_SECRET);
    }

    private function isTrustedStoragePid(string $pid, string $hmac): bool
    {
        return $pid !== '' && $hmac !== '' && hash_equals($this->hmacStoragePid($pid), $hmac);
    }

    private function isTrustedApprovalFlag(string $flag, string $hmac): bool
    {
        return $hmac !== '' && hash_equals($this->hmacApprovalFlag($flag), $hmac);
    }
}
