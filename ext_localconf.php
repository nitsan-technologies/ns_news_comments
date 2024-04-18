<?php

use Nitsan\NsNewsComments\Controller\CommentController;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
    ]
);

$iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
$iconRegistry->registerIcon(
    'ext-ns-comment-icon',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:ns_news_comments/Resources/Public/Icons/plug_comment.svg']
);

//Hooks for the news controller
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['Controller/NewsController.php']['overrideSettings']['ns_news_comments']
    = 'Nitsan\\NsNewsComments\\Hooks\\NewsController->modify';

ExtensionManagementUtility::addPageTSConfig('@import \'EXT:ns_news_comments/Configuration/page.tsconfig\'');
