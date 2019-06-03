<?php
return array(
    'ctrl' => array(
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
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'searchFields' => 'description,newsuid,username,usermail,childcomment',
        'iconfile' => 'EXT:ns_news_comments/Resources/Public/Icons/plug_comment.svg',
    ),
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, newsuid, username, usermail, paramlink, description,childcomment',
    ),
    'types' => array(
        '1' => array('showitem' => 'feuserid, accesstoken, username, usermail, userimage, description, paramlink, childcomment, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime'),
    ),
    'columns' => array(

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
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_nsnewscomments_domain_model_comment',
                'foreign_table_where' => 'AND tx_nsnewscomments_domain_model_comment.pid=###CURRENT_PID### AND tx_nsnewscomments_domain_model_comment.sys_language_uid IN (-1,0)',
            ),
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),

        't3ver_label' => array(
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ),
        ),

        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'starttime' => array(
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ),
            ),
        ),
        'endtime' => array(
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ),
            ),
        ),

        'newsuid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.newsuid',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'internal_type' => 'db',
                'foreign_table' => 'tx_news_domain_model_news',
                'allowed' => 'tx_news_domain_model_news',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'readOnly' => 1,
            ),
        ),

        'feuserid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.feuserid',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'readOnly' => 1,
                'eval' => 'trim',
                'readOnly' => 1,
            ),
        ),

        'accesstoken' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.accesstoken',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'readOnly' => 1,
                'eval' => 'trim',
                'readOnly' => 1,
            ),
        ),

        'username' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.username',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'readOnly' => 1,
                'eval' => 'trim',
            ),
        ),
        'usermail' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.usermail',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '256',
                'eval' => 'trim',
                'wizards' => array(
                    'link' => array(
                        'type' => 'popup',
                        'title' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.linkTitle',
                        'icon' => 'link_popup.gif',
                        'module' => array(
                            'name' => 'wizard_link',
                        ),
                        'JSopenParams' => 'height=800,width=600,status=0,menubar=0,scrollbars=1',
                    ),
                ),
                'readOnly' => 1,
                'softref' => 'typolink',
            ),
        ),
        'userimage' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.image',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'paramlink' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.paramlink',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'max' => '256',
                'eval' => 'trim',
                'wizards' => array(
                    'link' => array(
                        'type' => 'popup',
                        'title' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.linkTitle',
                        'icon' => 'link_popup.gif',
                        'module' => array(
                            'name' => 'wizard_link',
                        ),
                        'JSopenParams' => 'height=800,width=600,status=0,menubar=0,scrollbars=1',
                    ),
                ),
                'readOnly' => 1,
                'softref' => 'typolink',
            ),
        ),
        'description' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.comment',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ),
        'comment' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),

        'crdate' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.crdate',
            'config' => array(
                'type' => 'input',
            ),
        ),
        'childcomment' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:ns_news_comments/Resources/Private/Language/locallang_db.xlf:tx_nsnewscomments_domain_model_comment.childcomment',
            'config' => array(
                'type' => 'inline',
                'foreign_table' => 'tx_nsnewscomments_domain_model_comment',
                'foreign_field' => 'comment',
                'maxitems' => 9999,
                'appearance' => array(
                    'collapseAll' => true,
                    'newRecordLinkPosition' => 'none',
                    'levelLinksPosition' => 'none',
                    'useSortable' => false,
                    'enabledControls' => array(
                        'new' => false,
                        'dragdrop' => false,
                        'sort' => false,
                        'hide' => false,
                        'delete' => false,
                    ),
                ),
            ),
        ),
    ),
);
