<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'ns_news_comments',
    'Newscomment',
    [
        \Nitsan\NsNewsComments\Controller\CommentController::class => 'list, new, create',
    ],
    // non-cacheable actions
    [
        \Nitsan\NsNewsComments\Controller\CommentController::class => 'list, new, create',
    ]
);


$icons = [
    'ext-ns-comment-icon' => 'plug_comment.svg',
];
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
foreach ($icons as $identifier => $path) {
    $iconRegistry->registerIcon(
        $identifier,
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:ns_news_comments/Resources/Public/Icons/' . $path]
    );
}

//Hooks for the news controller
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['Controller/NewsController.php']['overrideSettings']['ns_news_comments']
    = 'Nitsan\\NsNewsComments\\Hooks\\NewsController->modify';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('@import \'EXT:ns_news_comments/Configuration/page.tsconfig\'');
