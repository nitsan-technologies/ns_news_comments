<?php

$corell = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:';

return [
    'ctrl' => [
        'title' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment',
        'label' => 'username',
        'descriptionColumn' => 'username',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'hideTable' => true,
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'ORDER BY uid DESC',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'description,newsuid,username,usermail,childcomment',
        'iconfile' => 'EXT:ns_news_comments/Resources/Public/Icons/plug_comment.svg',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '1' => ['showitem' => ' username, usermail, description, terms, paramlink, childcomment, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, sys_language_uid, l10n_diffsource, hidden, starttime, endtime'],
    ],
    'columns' => [

        'sys_language_uid' => [
            'exclude' => true,
            'label' => $corell . 'LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => $corell . 'LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => '', 'value' => 0],
                ],
                'foreign_table' => 'tx_nsnewscomments_domain_model_comment',
                'foreign_table_where' => 'AND tx_nsnewscomments_domain_model_comment.pid=###CURRENT_PID### AND tx_nsnewscomments_domain_model_comment.sys_language_uid IN (-1,0)',
            ],
        ],

        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        't3ver_label' => [
            'label' => $corell . 'LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],

        'hidden' => [
            'exclude' => true,
            'label' => $corell . 'LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => $corell . 'LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => $corell . 'LGL.endtime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
            ],
        ],

        'newsuid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.newsuid',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_news_domain_model_news',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'readOnly' => true,
            ],
        ],

        'username' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.username',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim',
            ],
        ],
        'usermail' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.usermail',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 256,
                'eval' => 'trim',
                'readOnly' => true,
                'softref' => 'typolink',
            ],
        ],
        'paramlink' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.paramlink',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 256,
                'eval' => 'trim',
                'readOnly' => true,
                'softref' => 'typolink',
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.comment',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'comment' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        'crdate' => [
            'exclude' => false,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.crdate',
            'config' => [
                'type' => 'datetime',
                'readOnly' => true,
            ],
        ],
        'childcomment' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.childcomment',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_nsnewscomments_domain_model_comment',
                'foreign_field' => 'comment',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => true,
                    'newRecordLinkPosition' => 'none',
                    'showAllLocalizationLink' => false,
                    'showSynchronizationLink' => false,
                    'showNewRecordLink' => false,
                    'useSortable' => false,
                    'enabledControls' => [
                        'new' => false,
                        'dragdrop' => false,
                        'sort' => false,
                        'hide' => false,
                        'delete' => false,
                    ],
                ],
            ],
        ],
        'terms' => [
            'exclude' => false,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.terms',
            'config' => [
                'type' => 'check',
                'readOnly' => true,
            ],
        ],
    ],
];
