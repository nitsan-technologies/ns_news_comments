<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
if (version_compare(TYPO3_branch, '10.0', '>=')) {
    $commentController = \Nitsan\NsNewsComments\Controller\CommentController::class;
} else {
    $commentController = 'Comment';
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Nitsan.ns_news_comments',
    'Newscomment',
    [
        $commentController => 'list, new, create',

    ],
    // non-cacheable actions
    [
        $commentController => 'list, new, create',
    ]
);

if (version_compare(TYPO3_branch, '7.0', '>')) {
    if (TYPO3_MODE === 'BE') {
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
    }
}
//Hooks for the news controller
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['Controller/NewsController.php']['overrideSettings']['ns_news_comments']
    = 'Nitsan\\NsNewsComments\\Hooks\\NewsController->modify';
