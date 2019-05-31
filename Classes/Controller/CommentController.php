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
use TYPO3\CMS\Core\Resource\ResourceFactory;
/**
 * CommentController
 */
class CommentController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
    
    /**
     * commentRepository
     *
     * @var \Nitsan\NsNewsComments\Domain\Repository\CommentRepository
     * @inject
     */
    protected $commentRepository = NULL;

    /**
     * @var \GeorgRinger\News\Domain\Repository\NewsRepository
     */
    protected $newsRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
    * User Repository
    *
    * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
    * @inject
    */
    protected $userRepository;

    /*
     * Inject a news repository to enable DI
     *
     * @param \GeorgRinger\News\Domain\Repository\NewsRepository $newsRepository
     * @return void
    */
    public function injectNewsRepository(\GeorgRinger\News\Domain\Repository\NewsRepository $newsRepository) {
        $this->newsRepository = $newsRepository;
    }

    /**
     * action initialize
     *
     * @return void
     */
    public function initializeAction() {
        $newsArr = GeneralUtility::_GP('tx_news_pi1');
        $newsUid = $newsArr['news'];
        $this->newsUid = intval($newsUid);

        // Storage page configuration
        $this->pageUid = $GLOBALS['TSFE']->id;
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        if($_REQUEST['tx_nsnewscomments_newscomment']['comments-storage-pid']){
            if($this->settings['mainConfiguration']['recordStoragePage']){
                $currentPid['persistence']['storagePid'] = $_REQUEST['tx_nsnewscomments_newscomment']['comments-storage-pid'];
            } else {
                $currentPid['persistence']['storagePid'] = GeneralUtility::_GP('id');
            }
            $this->configurationManager->setConfiguration(array_merge($extbaseFrameworkConfiguration, $currentPid));
        }else{
            if(empty($extbaseFrameworkConfiguration['persistence']['storagePid'])) {
                if($_REQUEST['tx_nsnewscomments_newscomment']){
                    $currentPid['persistence']['storagePid'] = $_REQUEST['tx_nsnewscomments_newscomment']['Storagepid'];
                } else {
                    if ($this->settings['relatedComments']) {
                        $currentPid['persistence']['storagePid'] = $this->settings['mainConfiguration']['recordStoragePage'];
                    } else {
                        $currentPid['persistence']['storagePid'] = GeneralUtility::_GP('id');
                    }
                }
                $this->configurationManager->setConfiguration(array_merge($extbaseFrameworkConfiguration, $currentPid));
            }
        }
    }

    /**
     * action list
     *
     *
     * @return void
     */
    public function listAction() {
        $relatedComments = $this->settings['relatedComments'];
        if ($relatedComments) {
            $this->settings['custom'] = false;
            $this->settings['dateFormat'] = $this->settings['mainConfiguration']['customDateFormat'];
            $this->settings['timeFormat'] = $this->settings['mainConfiguration']['customTimeFormat'];
            $this->settings['captcha'] = $this->settings['mainConfiguration']['disableCaptcha'];
            if($this->settings['mainConfiguration']['commentUserSettings'] == 'feuserOnly') {
                $this->settings['userSettings'] = $this->settings['mainConfiguration']['commentUserSettings'];
                $this->settings['feUserloginpid'] = $this->settings['mainConfiguration']['FEUserLoginPageId'];
            }else {
                $this->settings['userSettings'] = $this->settings['mainConfiguration']['commentUserSettings'];
            }
            $Image = $this->settings['mainConfiguration']['userImage'];
            $this->view->assign('relatedComments', TRUE);
        } else {
            $imageUid = $this->settings['usrimage']; 
            if(!empty($imageUid)){
               $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
               $fileReference = $resourceFactory->getFileReferenceObject($imageUid);
               $Image = $fileReference->getProperties();
            }
        }

        $this->contentObj = $this->configurationManager->getContentObject();
        $pid = $this->contentObj->data['pages'];
        if(empty($pid)) {
            $pid = GeneralUtility::_GP('id');
        }

        $setting = $this->settings;
        if ($this->newsUid) {
            $comments = $this->commentRepository->getCommentsByNews($newsId = $this->newsUid)->toArray();
            $path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('ns_news_comments') . 'Resources/Private/PHP/captcha.php';
            $verification = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('ns_news_comments') . 'Resources/Private/PHP/verify.php';
            $captcha_path = $path . '?' . rand();
            $this->view->assign('captcha_path', $captcha_path);
            $this->view->assign('verification', $verification);
            $this->view->assign('comments', $comments);
            $this->view->assign('newsID', $this->newsUid);
            $this->view->assign('pageid', $this->pageUid);
            $this->view->assign('Image', $Image);
            $this->view->assign('pid', $pid);
            $this->view->assign('settings', $setting);

            // User Login or not
            $userIDTest = $this->userRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']); 
            if($userIDTest){
                if($userIDTest->getName()){
                    $name = $userIDTest->getName();
                } else {
                    $name = $userIDTest->getUsername();
                }

                if($userIDTest->getEmail()){
                    $email = $userIDTest->getEmail();
                }

                if($userIDTest->getImage()){
                    if (!is_string($userIDTest->getImage())) {
                        $userimages= $userIDTest->getImage();
                    }
                }
                
                $this->view->assign('feuserlogin', 1);
                $this->view->assign('name', $name);
                $this->view->assign('email', $email);
                $this->view->assign('feuserImages', $userimages);
            }

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
    public function createAction(\Nitsan\NsNewsComments\Domain\Model\Comment $newComment) {
        $userIDTest = $this->userRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']); 
        if($userIDTest){
            if($userIDTest->getName()){
                $name = $userIDTest->getName();
            } else {
                $name = $userIDTest->getUsername();
            }
            $newComment->setUsername($name);

            if($userIDTest->getEmail()){
                $email = $userIDTest->getEmail();
                $newComment->setUsermail($email);
            }

            if($userIDTest->getImage()){
                if (is_string($userIDTest->getImage())) {
                    $userimages = explode(',', $userIDTest->getImage());
                    $newComment->setUserimage('uploads/pics/'.$userimages[0]);
                }
            }
            $newComment->setFeuserid($userIDTest->getUid());
        }

        $request = $this->request->getArguments();
        $adminEmail = $this->settings['notification']['siteadmin']['adminEmail'];
        $adminName = $this->settings['notification']['siteadmin']['adminName'];
        $emailSubject = $this->settings['notification']['siteadmin']['adminMailSubject'];
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
        $paramlink = $this->buildUriByUid($this->pageUid, $arguments = array('commentid' => $newComment->getUid()));
        $newComment->setParamlink($paramlink);
        $this->commentRepository->update($newComment);
        // Configuration for mail template
        $news = $this->newsRepository->findByUid($this->newsUid);
        $newsTitle = $news->getTitle();
        $translateArguments = array('comments' => $newComment, 'newsTitle' => $news->getTitle());
        $variables = array('UserData' => $translateArguments);
        if (isset($this->settings['notification']['siteadmin']['sendMailToAdmin'])) {
            $res = $this->sendTemplateEmail(array($adminEmail => $adminName), array($newComment->getUsermail() => $newComment->getUsername()), $emailSubject, 'mailTemplate', $variables);
        }
        // Disable comment for approvement
        if (isset($this->settings['approveComment']) && $this->settings['approveComment'] == 1) {
            $newComment->setHidden(1);
        } else {
            $this->persistenceManager->persistAll();
            $json[$newComment->getUid() ] = array('parentId' => $parentId, 'comment' => 'comment');
            return json_encode($json);
        }
    }

    /**
     * Returns a built URI by pageUid
     *
     * @param int $uid The uid to use for building link
     * @param bool $arguments
     * @return string The link
     */
    private function buildUriByUid($uid, $arguments = array()) {
        $newsUid = $this->newsUid;
        $commentid = $arguments['commentid'];
        $excludeFromQueryString = array('tx_nsnewscomments_newscomment[action]', 'tx_nsnewscomments_newscomment[controller]', 'tx_nsnewscomments_newscomment', 'type');
        $uri = $this->uriBuilder->reset()->setTargetPageUid($uid)->setAddQueryString(TRUE)->setArgumentsToBeExcludedFromQueryString($excludeFromQueryString)->setSection("comments-" . $commentid)->build();
        $uri = $this->addBaseUriIfNecessary($uri);
        return $uri;
    }

    /**
     * @param array $recipient recipient of the email in the format array('recipient@domain.tld' => 'Recipient Name')
     * @param array $sender sender of the email in the format array('sender@domain.tld' => 'Sender Name')
     * @param string $subject subject of the email
     * @param string $templateName template name (UpperCamelCase)
     * @param array $variables variables to be passed to the Fluid view
     */
    protected function sendTemplateEmail(array $recipient, array $sender, $subject, $templateName, array $variables = array()) {
        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailView */
        $emailView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        /*For use of Localize value */
        $extensionName = $this->request->getControllerExtensionName();
        $emailView->getRequest()->setControllerExtensionName($extensionName);
        /*For use of Localize value */
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPaths']['0']);
        $templatePathAndFilename = $templateRootPath . 'Email/' . $templateName . '.html';
        $emailView->setTemplatePathAndFilename($templatePathAndFilename);
        $emailView->assignMultiple($variables);
        $emailBody = $emailView->render();
        /** @var $message \TYPO3\CMS\Core\Mail\MailMessage */
        $message = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        /*Mail to Admin*/
        $message->setTo($recipient)->setFrom($sender)->setSubject($subject);
        // HTML Email
        $message->setBody($emailBody, 'text/html');
        // $status = 0;
        $message->send();
        $status = $message->isSent();
        return $status;
    }
}
