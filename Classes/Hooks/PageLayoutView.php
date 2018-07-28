<?php
namespace Nitsan\NsNewsComments\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class PageLayoutView implements \TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface {

    public function preProcess(\TYPO3\CMS\Backend\View\PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row) {
        if ($row['CType'] == 'list' && $row['list_type'] == 'nsnewscomments_newscomment') {

            $drawItem = false;

            $headerContent = 
                "<table class='table table-condensed table-hover news-table'><thead><tr><th colspan='2'>".LocalizationUtility::translate('pi1_title','ns_news_comments')."</th></tr></thead>" ;

            $ffXml = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($row['pi_flexform']);

            $itemContent= "<tbody>";

            if($ffXml['data']['sDEF']['lDEF']['settings.custom']['vDEF']!=1){
                $itemContent .= "<tr>
                                    <th>".LocalizationUtility::translate('backend.dateformate','ns_news_comments')."</th>
                                    <td style='padding-left: 10px;'>".$ffXml['data']['sDEF']['lDEF']['settings.dateFormat']['vDEF']."</td>
                                </tr>";

                $itemContent .= "<tr>
                                    <th>".LocalizationUtility::translate('backend.timeformate','ns_news_comments')."</th>
                                    <td style='padding-left: 10px;'>".$ffXml['data']['sDEF']['lDEF']['settings.timeFormat']['vDEF']."</td>
                                </tr>";
            } else {
                $itemContent .= "<tr>
                                    <th>".LocalizationUtility::translate('backend.dateformate','ns_news_comments')."</th>
                                    <td style='padding-left: 10px;'>".$ffXml['data']['sDEF']['lDEF']['settings.customdate']['vDEF']."</td>
                                </tr>";

                $itemContent .="<tr>
                                    <th>".LocalizationUtility::translate('backend.timeformate','ns_news_comments')."</th>
                                    <td style='padding-left: 10px;'>".$ffXml['data']['sDEF']['lDEF']['settings.customtime']['vDEF']."</td>
                                </tr>";
            }

            if($ffXml['data']['sDEF']['lDEF']['settings.captcha']['vDEF']){
                $itemContent .= "<tr>
                                    <th>".LocalizationUtility::translate('backend.captcha','ns_news_comments')."</th>
                                    <td style='padding-left: 10px;'><i class='fa fa-check'></i></td>
                                </tr>";
            } 

            if($ffXml['data']['sDEF']['lDEF']['settings.usrimage']['vDEF']){
                $itemContent .= "<tr>
                                    <th>".LocalizationUtility::translate('backend.Image','ns_news_comments')."</th>
                                    <td style='padding-left: 10px;'><i class='fa fa-check'></i></td>
                                </tr>";
            } 


            $itemContent .= "</tbody></table>";
        }
    }
}