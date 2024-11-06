<?php

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

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\Environment;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Install\Service\SessionService;
use Nitsan\NsNewsComments\Domain\Model\Comment;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use GeorgRinger\News\Domain\Repository\NewsRepository;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Resource\Exception\InvalidFileException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Nitsan\NsNewsComments\Domain\Repository\CommentRepository;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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

    protected int $newsUid;

    protected int $pageUid;

    /**
     * @var array
     */
    protected array $typo3VersionArray = [];


    public function __construct(
        CommentRepository  $commentRepository,
        PersistenceManager $persistenceManager,
        NewsRepository     $newsRepository
    ) {
        $this->commentRepository = $commentRepository;
        $this->persistenceManager = $persistenceManager;
        $this->newsRepository = $newsRepository;
    }

    /**
     * action initialize
     *
     * @return void
     */
    public function initializeAction(): void
    {
        $sessionService = GeneralUtility::makeInstance(SessionService::class);
        $sessionService->startSession();
        $this->typo3VersionArray = VersionNumberUtility::convertVersionStringToArray(VersionNumberUtility::getCurrentTypo3Version());
        $getData = $this->request->getQueryParams();
        $postData = $this->request->getParsedBody();
        $requestData = array_merge($getData, (array)$postData);
        $newsArr = $requestData['tx_news_pi1'] ?? [];
        $newsUid = '';
        if (is_null($newsArr)) {
            if (isset($_SESSION['params']) && $_SESSION['params']['originalSettings']['singleNews']) {
                $newsUid = $_SESSION['params']['originalSettings']['singleNews'];
            }
        } else {
            $newsUid = $newsArr['news'] ?? null;
        }
        $this->newsUid = (int)$newsUid;

        // Storage page configuration
        // @extensionScannerIgnoreLine
        $this->pageUid = $GLOBALS['TSFE']->id;
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

        if (empty($extbaseFrameworkConfiguration['persistence']['storagePid'])) {
            if (isset($_REQUEST['tx_nsnewscomments_newscomment']['Storagepid'])) {
                $currentPid['persistence']['storagePid'] = $_REQUEST['tx_nsnewscomments_newscomment']['Storagepid'];
            } else {
                if (isset($this->settings['storagePid']) && !empty($this->settings['storagePid'])) {
                    $currentPid['persistence']['storagePid'] = $this->settings['storagePid'];
                } else {
                    $currentPid['persistence']['storagePid'] = $this->pageUid;
                }
            }
            $this->configurationManager->setConfiguration(array_merge($extbaseFrameworkConfiguration, $currentPid));
        }
    }

    /**
     * action list
     *
     * @return ResponseInterface
     * @throws InvalidFileException
     */
    public function listAction(): ResponseInterface
    {
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

        if (empty($extbaseFrameworkConfiguration['persistence']['storagePid'])) {
            $pid = $this->pageUid;
        } else {
            $pid = $extbaseFrameworkConfiguration['persistence']['storagePid'];
        }
        $setting = $this->settings;
        if ($this->newsUid) {
            $comments = $this->commentRepository->getCommentsByNews($this->newsUid)->toArray();

            if ($this->settings['captcha'] == '0') {
                $paths = $this->captchaVerificationPath();
                $captcha_path = $paths['captcha'] . '?' . rand();
                $this->view->assignMultiple([
                    'captcha_path' => $captcha_path,
                    'verification' => $paths['verification'],
                ]);
            }

            $this->view->assignMultiple([
                'comments' => $comments,
                'newsID' => $this->newsUid,
                'pageid' => $this->pageUid,
                'pid' => $pid,
                'settings' => $setting,
            ]);

        } else {
            $error = LocalizationUtility::translate('tx_nsnewscomments_domain_model_comment.errorMessage', 'NsNewsComments');
            if (version_compare((string)$this->typo3VersionArray['version_main'], '11', '>')) {
                $this->addFlashMessage($error, '', ContextualFeedbackSeverity::ERROR);
            } else {
                // @extensionScannerIgnoreLine
                $this->addFlashMessage($error, '', AbstractMessage::ERROR);
            }
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
        $request = $this->request->getArguments();
        $newComment->setCrdate(time());
        $language = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('language', 'id');
        $newComment->setSysLanguageUid($language);
        $parentId = $request['parentId'];
        if ($request['parentId'] > 0) {
            $childComment = $this->commentRepository->findByUid($parentId);
            $childComment->addChildcomment($newComment);
            $this->commentRepository->update($childComment);
        }

        // Add comment to repository
        $this->commentRepository->add($newComment);
        $this->persistenceManager->persistAll();
        $news = $this->newsRepository->findByUid($newComment->getNewsuid());


        // Add paramlink to comments for scrolling to comment
        $paramLink = $this->buildUriByUid(
            $this->pageUid,
            $news,
            ['commentid' => $newComment->getUid()]
        );
        $newComment->setParamlink($paramLink);
        $this->commentRepository->update($newComment);

        $this->persistenceManager->persistAll();
        $json[$newComment->getUid()] = ['parentId' => $parentId, 'comment' => 'comment'];
        return $this->jsonResponse(json_encode($json));
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
            'tx_nsnewscomments_newscomment', 'type'
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
     * @throws InvalidFileException
     */
    public function getPath($path, $extName): string
    {
        $arguments = ['path' => $path, 'extensionName' => $extName];
        $path = $arguments['path'];
        $publicPath = sprintf('EXT:%s/Resources/Public/%s', $arguments['extensionName'], ltrim($path, '/'));
        $uri = PathUtility::getPublicResourceWebPath($publicPath);
        return substr($uri, 1);
    }


    /**
     * @return array
     * @throws InvalidFileException
     */
    private function captchaVerificationPath(): array
    {
        $paths = [];
        if (Environment::isComposerMode()) {
            $assetPath = $this->getPath('PHP/', 'ns_news_comments');
            $basePath = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
            $paths['captcha'] = $basePath . $assetPath . 'captcha.php';
            $paths['verification'] = $basePath . $assetPath . 'verify.php';
        } else {
            $basePath = PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath('ns_news_comments'));
            $paths['captcha'] = $basePath . 'Resources/Public/PHP/captcha.php';
            $paths['verification'] = $basePath . 'Resources/Public/PHP/verify.php';
        }
        return $paths;
    }

}
