<?php

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

$_EXTKEY = 'ns_news_comments';

/***************
 * Plugin
 */
$ctypeKey = ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'Newscomment',
    'News Comment',
    'ext-ns-comment-icon',
    'plugins'
);

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;Configuration,pi_flexform,pages',
    $ctypeKey,
    'after:subheader',
);

$flexFormFile = 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForm/FlexForm.xml';
if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() >= 14) {
    $GLOBALS['TCA']['tt_content']['types'][$ctypeKey]['columnsOverrides']['pi_flexform']['config']['ds'] = $flexFormFile;
} else {
    // @extensionScannerIgnoreLine
    ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        $flexFormFile,
        $ctypeKey,
    );
}
