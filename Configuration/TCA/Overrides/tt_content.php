<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

$_EXTKEY = 'ns_news_comments';

/***************
 * Plugin
 */
ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'Newscomment',
    'News Comment'
);

/* Flexform setting  */
$pluginSignature = str_replace('_', '', $_EXTKEY) . '_' . 'newscomment';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'recursive,select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForm/FlexForm.xml');
