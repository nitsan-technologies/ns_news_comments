<?php

namespace Nitsan\NsNewsComments\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2018
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
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
     *
     * @var CommentRepository
     */
    protected $commentRepository = null;

    /**
     * @var NewsRepository
     */
    protected $newsRepository;

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    protected $newsUid;

    protected $pageUid;

    /*
     * Inject a news repository to enable DI
     *
     * @param \GeorgRinger\News\Domain\Repository\NewsRepository $newsRepository
     * @return void
     */
    public function injectNewsRepository(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    /**
    * Inject a news repository to enable DI
    *
    * @param CommentRepository $commentRepository
    */
    public function injectCommentRepository(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * Inject a news repository to enable DI
     *
     * @param PersistenceManager $persistenceManager
     */
    public function injectPersistenceManager(PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * action initialize
     *
     * @return void
     */
    public function initializeAction()
    {
        $sessionService = GeneralUtility::makeInstance(\TYPO3\CMS\Install\Service\SessionService::class);
        $sessionService->startSession();
        $newsArr = GeneralUtility::_GP('tx_news_pi1');
        $newsUid = '';
        if (is_null($newsArr)) {
            if (isset($_SESSION['params']) && $_SESSION['params']['originalSettings']['singleNews']) {
                $newsUid = $_SESSION['params']['originalSettings']['singleNews'];
            }
        } else {
            $newsUid = $newsArr['news'];
        }
        $this->newsUid = intval($newsUid);

        // Storage page configuration
        $this->pageUid = $GLOBALS['TSFE']->id;
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

        if (empty($extbaseFrameworkConfiguration['persistence']['storagePid'])) {
            if ($_REQUEST['tx_nsnewscomments_newscomment']) {
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
     * @return void
     */
    public function listAction()
    {
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

        if (empty($extbaseFrameworkConfiguration['persistence']['storagePid'])) {
            $pid = $GLOBALS['TSFE']->id;
        } else {
            $pid = $extbaseFrameworkConfiguration['persistence']['storagePid'];
        }
        $setting = $this->settings;
        if ($this->newsUid) {
            $comments = $this->commentRepository->getCommentsByNews($newsId = $this->newsUid)->toArray();
            $paths = $this->captchaVerificationPath();

            $captcha_path = $paths['captcha'] . '?' . rand();
            $this->view->assign('captcha_path', $captcha_path);
            $this->view->assign('verification', $paths['verification']);
            $this->view->assign('comments', $comments);
            $this->view->assign('newsID', $this->newsUid);
            $this->view->assign('pageid', $this->pageUid);
            $this->view->assign('pid', $pid);
            $this->view->assign('settings', $setting);
        } else {
            $error = LocalizationUtility::translate('tx_nsnewscomments_domain_model_comment.errorMessage', 'NsNewsComments');
            $this->addFlashMessage($error, '', AbstractMessage::ERROR);
        }
    }

    /**
     * action create
     *
     * @param Comment $newComment
     *
     * @return void
     */
    public function createAction(Comment $newComment)
    {
        $request = $this->request->getArguments();
        $newComment->setCrdate(time());
        $newComment->set_languageUid($GLOBALS['TSFE']->sys_language_uid);
        $parentId = $request['parentId'];
        if ($request['parentId'] > 0) {
            $childComment = $this->commentRepository->findByUid($parentId);
            $childComment->addChildcomment($newComment);
            $this->commentRepository->update($childComment);
        }

        // Add comment to repository
        $this->commentRepository->add($newComment);
        $this->persistenceManager->persistAll();

        // Add paramlink to comments for scrolling to comment
        $paramLink = $this->buildUriByUid($this->pageUid, $arguments = ['commentid' => $newComment->getUid()]);
        $newComment->setParamlink($paramLink);
        $this->commentRepository->update($newComment);

        $this->persistenceManager->persistAll();
        $json[$newComment->getUid()] = ['parentId' => $parentId, 'comment' => 'comment'];
        return json_encode($json);
    }

    /**
     * Returns a built URI by pageUid
     *
     * @param int $uid The uid to use for building link
     * @param array $arguments
     * @return string The link
     */
    private function buildUriByUid($uid, $arguments = []): string
    {
        $commentId = $arguments['commentid'];
        $excludeFromQueryString = [
            'tx_nsnewscomments_newscomment[action]',
            'tx_nsnewscomments_newscomment[controller]',
            'tx_nsnewscomments_newscomment',
            'type'
        ];
        $uri = $this->uriBuilder->reset()->setTargetPageUid($uid)->setAddQueryString(true)->setArgumentsToBeExcludedFromQueryString($excludeFromQueryString)->setSection('comments-' . $commentId)->build();
        return $this->addBaseUriIfNecessary($uri);
    }

    /**
     * @return array
     */
    private function captchaVerificationPath(): array
    {
        $paths = [];
        if (version_compare(TYPO3_branch, '9.0', '>')) {
            $paths['captcha'] = \TYPO3\CMS\Core\Utility\PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath('ns_news_comments')) . 'Resources/Private/PHP/captcha.php';
            $paths['verification'] = \TYPO3\CMS\Core\Utility\PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath('ns_news_comments')) . 'Resources/Private/PHP/verify.php';
        } else {
            $paths['captcha'] = ExtensionManagementUtility::siteRelPath('ns_news_comments') . 'Resources/Private/PHP/captcha.php';
            $paths['verification'] = ExtensionManagementUtility::siteRelPath('ns_news_comments') . 'Resources/Private/PHP/verify.php';
        }
        return $paths;
    }
}
