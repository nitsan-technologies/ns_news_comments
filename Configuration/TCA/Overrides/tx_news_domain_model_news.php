<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

$fields = [
    'comments' => [
        'exclude' => true,
        'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.comments',
        'config' => [
            'type' => 'inline',
            'foreign_table' => 'tx_nsnewscomments_domain_model_comment',
            'foreign_field' => 'newsuid',
            'maxitems' => 9999,
            'appearance' => [
                'collapseAll' => 1,
                'levelLinksPosition' => 'top',
                'showNewRecordLink' => false,
                'showSynchronizationLink' => 1,
                'showPossibleLocalizationRecords' => 1,
                'useSortable' => 1,
                'showAllLocalizationLink' => 1,
                'enabledControls' => [
                    'new' => false,
                ],
            ],
        ],
    ],
];

// add field to tca
ExtensionManagementUtility::addTCAcolumns(
    'tx_news_domain_model_news',
    $fields,
);

// add new field subtitle after title
ExtensionManagementUtility::addToAllTCAtypes(
    'tx_news_domain_model_news',
    '--div--;LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.newComments,comments',
    '',
    ''
);
