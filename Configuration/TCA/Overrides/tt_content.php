<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
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

// @extensionScannerIgnoreLine
ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForm/FlexForm.xml',
    $ctypeKey,
);
