<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if ((GeneralUtility::makeInstance(Typo3Version::class))->getMajorVersion() == 11) {
    // @extensionScannerIgnoreLine
    ExtensionManagementUtility::addLLrefForTCAdescr('tx_nsnewscomments_domain_model_comment', 'EXT:ns_news_comments/Resources/Private/Language/locallang_csh_tx_newscomment_domain_model_comment.xlf');
    // @extensionScannerIgnoreLine
    ExtensionManagementUtility::allowTableOnStandardPages('tx_nsnewscomments_domain_model_comment');
}
