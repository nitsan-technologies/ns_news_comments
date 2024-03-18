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

use GeorgRinger\News\Domain\Repository\NewsRepository;
use Nitsan\NsNewsComments\Domain\Model\Comment;
use Nitsan\NsNewsComments\Domain\Repository\CommentRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Install\Service\SessionService;

/**
 * CommentController
 */
class CommentController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * commentRepository
     */
    protected $commentRepository = null;

    /**
     * @var \GeorgRinger\News\Domain\Repository\NewsRepository
     */
    protected $newsRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager;


    protected $newsId;

    protected $newsUid;

    protected $pageUid;

    /**
     * @var array
     */
    protected array $typo3VersionArray = [];


    public function __construct(
        CommentRepository      $commentRepository,
        PersistenceManager     $persistenceManager,
        NewsRepository   $newsRepository
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
        $requestData = array_merge((array)$getData, (array)$postData);
        $newsArr = $requestData['tx_news_pi1'] ?? [];
        $newsUid = '';
        if (is_null($newsArr)) {
            if (isset($_SESSION['params']) && $_SESSION['params']['originalSettings']['singleNews']) {
                $newsUid = $_SESSION['params']['originalSettings']['singleNews'];
            }
        } else {
            $newsUid = $newsArr['news'] ?? null;
        }
        $this->newsUid = intval($newsUid);

        // Storage page configuration
        $this->pageUid = $GLOBALS['TSFE']->id;
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

        if (empty($extbaseFrameworkConfiguration['persistence']['storagePid'])) {
            if (isset($_REQUEST['tx_nsnewscomments_newscomment']['Storagepid'])) {
                $currentPid['persistence']['storagePid'] = $_REQUEST['tx_nsnewscomments_newscomment']['Storagepid'];
            } else {
                if ($this->settings['storagePid']) {
                    $currentPid['persistence']['storagePid'] = $this->settings['storagePid'];
                } else {
                    $currentPid['persistence']['storagePid'] = $GLOBALS['TSFE']->id;
                }
            }
            $this->configurationManager->setConfiguration(array_merge($extbaseFrameworkConfiguration, $currentPid));
        }
    }

    /**
     * action list
     *
     * @return ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

        if (empty($extbaseFrameworkConfiguration['persistence']['storagePid'])) {
            $pid = $GLOBALS['TSFE']->id;
        } else {
            $pid = $extbaseFrameworkConfiguration['persistence']['storagePid'];
        }
        $setting = $this->settings;
        if ($this->newsUid) {
            $comments = $this->commentRepository->getCommentsByNews($newsId = $this->newsUid)->toArray();
            if (Environment::isComposerMode()) {
                $assetPath = $this->getPath('PHP/', 'ns_news_comments');
                $path = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . $assetPath . 'captcha.php';
                $verification = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . $assetPath . 'verify.php';
            } else {
                $path = PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath('ns_news_comments')) . 'Resources/Public/PHP/captcha.php';
                $verification = PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath('ns_news_comments')) . 'Resources/Public/PHP/verify.php';
            }
            $captcha_path = $path . '?' . rand();
            $this->view->assignMultiple([
                'captcha_path' => $captcha_path,
                'verification' => $verification,
                'comments' => $comments,
                'newsID' => $this->newsUid,
                'pageid' => $this->pageUid,
                'pid' => $pid,
                'settings' => $setting,
            ]);

        } else {
            $error = LocalizationUtility::translate('tx_nsnewscomments_domain_model_comment.errorMessage', 'NsNewsComments');
            if (version_compare((string)$this->typo3VersionArray['version_main'], '11', '>')) {
                $this->addFlashMessage($error, '', \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR);
            } else {
                $this->addFlashMessage($error, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
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
        $paramlink = $this->buildUriByUid((int)$this->pageUid, $news, $arguments = ['commentid' => $newComment->getUid()]);
        $newComment->setParamlink($paramlink);
        $this->commentRepository->update($newComment);

        $this->persistenceManager->persistAll();
        $json[$newComment->getUid()] = ['parentId' => $parentId, 'comment' => 'comment'];
        return $this->jsonResponse(json_encode($json));
    }

    /**
     * Returns a built URI by pageUid
     *
     * @param int $uid The uid to use for building link
     * @param mixed $news
     * @param array $arguments
     * @return string The link
     */
    private function buildUriByUid(int $uid, $news, $arguments = []): string
    {
        $commentid = $arguments['commentid'];
        $excludeFromQueryString = ['tx_nsnewscomments_newscomment[action]', 'tx_nsnewscomments_newscomment[controller]', 'tx_nsnewscomments_newscomment', 'type'];
        $this->uriBuilder->reset()->setTargetPageUid($uid)->setAddQueryString(true)->setArgumentsToBeExcludedFromQueryString($excludeFromQueryString)->setSection('comments-' . $commentid);
        if (array_key_exists('formail', $arguments)) {
            $this->uriBuilder->setArguments(['frommail' => 1]);
        }
        $uri = $this->uriBuilder->uriFor('detail', ['news' => $news], 'News', 'News', 'Pi1');
        $uri = $this->addBaseUriIfNecessary($uri);
        return $uri;
    }


    /**
     * getPath for composer based setup
     * @param mixed $path
     * @param mixed $extName
     * @return string
     */
    public function getPath(mixed $path, mixed $extName): string
    {
        $arguments = ['path' => $path, 'extensionName' => $extName];
        $path = $arguments['path'];
        $publicPath = sprintf('EXT:%s/Resources/Public/%s', $arguments['extensionName'], ltrim($path, '/'));
        $uri = PathUtility::getPublicResourceWebPath($publicPath);
        return substr($uri, 1);
    }
}
