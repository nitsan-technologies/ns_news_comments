<?php

use Nitsan\NsNewsComments\Controller\CommentController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

$ajaxPagesTypoScript = (string)file_get_contents(
    ExtensionManagementUtility::extPath('ns_news_comments')
    . 'Configuration/TypoScript/AjaxPages.typoscript'
);
$typo3Version = VersionNumberUtility::convertVersionStringToArray(
    VersionNumberUtility::getCurrentTypo3Version()
);
if ((int)$typo3Version['version_main'] >= 13) {
    ExtensionManagementUtility::addTypoScriptSetup($ajaxPagesTypoScript, true);
} else {
    ExtensionManagementUtility::addTypoScriptSetup($ajaxPagesTypoScript);
}

ExtensionUtility::configurePlugin(
    'ns_news_comments',
    'Newscomment',
    [
        CommentController::class => 'list, new, create',
    ],
    // non-cacheable actions
    [
        CommentController::class => 'list, new, create',
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeResolver'][1791957358] = [
    'nodeName' => 'flexFormNoTabsContainer',
    'priority' => 40,
    'class' => \Nitsan\NsNewsComments\Form\Resolver\PremiumStripFlexFormNoTabsContainerResolver::class,
];
