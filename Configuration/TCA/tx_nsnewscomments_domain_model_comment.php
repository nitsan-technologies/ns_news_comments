<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment',
        'label' => 'username',
        'descriptionColumn' => 'username',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'hideTable' => true,
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
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
            'ignorePageTypeRestriction' => true
        ],
    ],
    'types' => [
        '1' => ['showitem' => ' username, usermail, description, terms, paramlink, childcomment, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, sys_language_uid, l10n_diffsource, hidden, starttime, endtime'],
    ],
    'columns' => [

        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple',
                    ],
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
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
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],

        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ],
            ],
        ],

        'newsuid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.newsuid',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'internal_type' => 'db',
                'foreign_table' => 'tx_news_domain_model_news',
                'allowed' => 'tx_news_domain_model_news',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'readOnly' => 1,
            ],
        ],

        'username' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.username',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => 1,
                'eval' => 'trim',
            ],
        ],
        'usermail' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.usermail',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '256',
                'eval' => 'trim',
                'wizards' => [
                    'link' => [
                        'type' => 'popup',
                        'title' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.linkTitle',
                        'icon' => 'link_popup.gif',
                        'module' => [
                            'name' => 'wizard_link',
                        ],
                        'JSopenParams' => 'height=800,width=600,status=0,menubar=0,scrollbars=1',
                    ],
                ],
                'readOnly' => 1,
                'softref' => 'typolink',
            ],
        ],
        'paramlink' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.paramlink',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '256',
                'eval' => 'trim',
                'wizards' => [
                    'link' => [
                        'type' => 'popup',
                        'title' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.linkTitle',
                        'icon' => 'link_popup.gif',
                        'module' => [
                            'name' => 'wizard_link',
                        ],
                        'JSopenParams' => 'height=800,width=600,status=0,menubar=0,scrollbars=1',
                    ],
                ],
                'readOnly' => 1,
                'softref' => 'typolink',
            ],
        ],
        'description' => [
            'exclude' => 1,
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
            'exclude' => 0,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.crdate',
            'config' => [
                'type' => 'input',
            ],
        ],
        'childcomment' => [
            'exclude' => 1,
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
            'exclude' => 0,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.terms',
            'config' => [
                'type' => 'check',
                'readOnly' => 1,
            ],
        ],
    ],
];
