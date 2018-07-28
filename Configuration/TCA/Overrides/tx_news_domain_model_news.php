<?php
defined('TYPO3_MODE') || die();


$GLOBALS['TCA']['tx_news_domain_model_news']['interface']['showRecordFieldList'] = $GLOBALS['TCA']['tx_news_domain_model_news']['interface']['showRecordFieldList'].',comments';
$GLOBALS['TCA']['tx_news_domain_model_news']['types']['0']['showitem'] = $GLOBALS['TCA']['tx_news_domain_model_news']['types']['0']['showitem'].",--div--;Comments,comments";
$GLOBALS['TCA']['tx_news_domain_model_news']['types']['0']['showitem'] = $GLOBALS['TCA']['tx_news_domain_model_news']['types']['0']['showitem'].",--div--;Comments";

$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['comments'] = array(
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
            'showSynchronizationLink' => 1,
            'showPossibleLocalizationRecords' => 1,
            'useSortable' => 1,
            'showAllLocalizationLink' => 1,
            'enabledControls' => [
                'new' => false,
            ],
        ],
    ],
);
