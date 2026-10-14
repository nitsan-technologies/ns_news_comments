<?php

use Nitsan\NsNewsComments\Controller\CommentController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
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

//Hooks for the news controller
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['Controller/NewsController.php']['overrideSettings']['ns_news_comments']
    = 'Nitsan\\NsNewsComments\\Hooks\\NewsController->modify';

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeResolver'][1791957358] = [
    'nodeName' => 'flexFormNoTabsContainer',
    'priority' => 40,
    'class' => \Nitsan\NsNewsComments\Form\Resolver\PremiumStripFlexFormNoTabsContainerResolver::class,
];
