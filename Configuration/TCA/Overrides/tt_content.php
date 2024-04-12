<?php

defined('TYPO3_MODE') or die();

$_EXTKEY = 'ns_news_comments';

/***************
 * Plugin
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Nitsan.' . $_EXTKEY,
    'Newscomment',
    'News Comment'
);

/* Flexform setting  */
$pluginSignature = str_replace('_', '', $_EXTKEY) . '_' . 'newscomment';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'recursive,select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForm/FlexForm.xml');
