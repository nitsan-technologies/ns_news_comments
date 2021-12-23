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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

session_start();
/**
 * CommentController
 */
class CommentController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * commentRepository
     *
     * @var \Nitsan\NsNewsComments\Domain\Repository\CommentRepository
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

    /**
     * User Repository
     *
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     */
    protected $userRepository;

    /*
     * Inject a news repository to enable DI
     *
     * @param \GeorgRinger\News\Domain\Repository\NewsRepository $newsRepository
     * @return void
     */
    public function injectNewsRepository(\GeorgRinger\News\Domain\Repository\NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    /**
    * Inject a news repository to enable DI
    *
    * @param \Nitsan\NsNewsComments\Domain\Repository\CommentRepository $commentRepository
    */
    public function injectCommentRepository(\Nitsan\NsNewsComments\Domain\Repository\CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * Inject a news repository to enable DI
     *
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager
     */
    public function injectPersistenceManager(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * Inject a news repository to enable DI
     *
     * @param \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository $userRepository
     */
    public function injectUserRepository(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * action initialize
     *
     * @return void
     */
    public function initializeAction()
    {
        $newsArr = GeneralUtility::_GP('tx_news_pi1');
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
            if (version_compare(TYPO3_branch, '9.0', '>')) {
                $path = \TYPO3\CMS\Core\Utility\PathUtility::stripPathSitePrefix(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('ns_news_comments')) . 'Resources/Private/PHP/captcha.php';
                $verification = \TYPO3\CMS\Core\Utility\PathUtility::stripPathSitePrefix(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('ns_news_comments')) . 'Resources/Private/PHP/verify.php';
            } else {
                $path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('ns_news_comments') . 'Resources/Private/PHP/captcha.php';
                $verification = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('ns_news_comments') . 'Resources/Private/PHP/verify.php';
            }
            $captcha_path = $path . '?' . rand();
            $this->view->assign('captcha_path', $captcha_path);
            $this->view->assign('verification', $verification);
            $this->view->assign('comments', $comments);
            $this->view->assign('newsID', $this->newsUid);
            $this->view->assign('pageid', $this->pageUid);
            $this->view->assign('pid', $pid);
            $this->view->assign('settings', $setting);
        } else {
            $error = LocalizationUtility::translate('tx_nsnewscomments_domain_model_comment.errorMessage', 'NsNewsComments');
            $this->addFlashMessage($error, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        }
    }

    /**
     * action create
     *
     * @param \Nitsan\NsNewsComments\Domain\Model\Comment $newComment
     *
     * @return void
     */
    public function createAction(\Nitsan\NsNewsComments\Domain\Model\Comment $newComment)
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
        $paramlink = $this->buildUriByUid($this->pageUid, $arguments = ['commentid' => $newComment->getUid()]);
        $newComment->setParamlink($paramlink);
        $this->commentRepository->update($newComment);

        $this->persistenceManager->persistAll();
        $json[$newComment->getUid()] = ['parentId' => $parentId, 'comment' => 'comment'];
        return json_encode($json);
    }

    /**
     * Returns a built URI by pageUid
     *
     * @param int $uid The uid to use for building link
     * @param bool $arguments
     * @return string The link
     */
    private function buildUriByUid($uid, $arguments = [])
    {
        $newsUid = $this->newsUid;
        $commentid = $arguments['commentid'];
        $excludeFromQueryString = ['tx_nsnewscomments_newscomment[action]', 'tx_nsnewscomments_newscomment[controller]', 'tx_nsnewscomments_newscomment', 'type'];
        $uri = $this->uriBuilder->reset()->setTargetPageUid($uid)->setAddQueryString(true)->setArgumentsToBeExcludedFromQueryString($excludeFromQueryString)->setSection('comments-' . $commentid)->build();
        $uri = $this->addBaseUriIfNecessary($uri);
        return $uri;
    }

    /**
     * Returns a built URI by buildUriForAccesstoken
     *
     * @param int $uid The uid to use for building link
     * @param bool $arguments
     * @return string The link
     */
    private function buildUriForAccesstoken($uid, $arguments = [])
    {
        $newsUid = $this->newsUid;
        $excludeFromQueryString = ['tx_nsnewscomments_newscomment[action]', 'tx_nsnewscomments_newscomment[controller]', 'tx_nsnewscomments_newscomment', 'type'];
        $uri = $this->uriBuilder->reset()->setTargetPageUid($uid)->setAddQueryString(true)->setArgumentsToBeExcludedFromQueryString($excludeFromQueryString)->setArguments($arguments)->build();
        $uri = $this->addBaseUriIfNecessary($uri);
        return $uri;
    }
}
