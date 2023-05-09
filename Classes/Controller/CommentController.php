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

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

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
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $newsId;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $newsUid;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $pageUid;

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
     * action initialize
     *
     * @return void
     */
    public function initializeAction()
    {
        $sessionService = GeneralUtility::makeInstance(\TYPO3\CMS\Install\Service\SessionService::class);
        $sessionService->startSession();
        $getData = $this->request->getQueryParams();
        $postData = $this->request->getParsedBody();
        $requestData = array_merge((array)$getData,(array)$postData);
        $newsArr = isset($requestData['tx_news_pi1']) ? $requestData['tx_news_pi1'] : [] ;
        if (is_null($newsArr)) {
            if (isset($_SESSION['params']) && $_SESSION['params']['originalSettings']['singleNews']) {
                $newsUid = $_SESSION['params']['originalSettings']['singleNews'];
            }
        } else {
            $newsUid = isset($newsArr['news']) ? $newsArr['news'] : null;
        }
        $this->newsUid = intval($newsUid);

        // Storage page configuration
        $this->pageUid = $GLOBALS['TSFE']->id;
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

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
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

        if (empty($extbaseFrameworkConfiguration['persistence']['storagePid'])) {
            $pid = $GLOBALS['TSFE']->id;
        } else {
            $pid = $extbaseFrameworkConfiguration['persistence']['storagePid'];
        }
        $setting = $this->settings;
        if ($this->newsUid) {
            $comments = $this->commentRepository->getCommentsByNews($newsId = $this->newsUid)->toArray();
            $path = PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath('ns_news_comments')) . 'Resources/Private/PHP/captcha.php';
            $verification = PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath('ns_news_comments')) . 'Resources/Private/PHP/verify.php';
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
            $this->addFlashMessage($error, '',\TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR);
        }
        return $this->htmlResponse();
    }

    /**
     * action create
     *
     * @param \Nitsan\NsNewsComments\Domain\Model\Comment $newComment
     *
     * @return ResponseInterface
     */
    public function createAction(\Nitsan\NsNewsComments\Domain\Model\Comment $newComment): ResponseInterface
    {
        $request = $this->request->getArguments();
        $newComment->setCrdate(time());
        $languageAspect = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class)->getAspect('language');
        $language = $languageAspect->getId();
        $newComment->set_languageUid($language);
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
        $paramlink = $this->buildUriByUid($this->pageUid, $news,$arguments = ['commentid' => $newComment->getUid()]);
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
     * @param bool $arguments
     * @return string The link
     */
    private function buildUriByUid($uid,$news, $arguments = [])
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
