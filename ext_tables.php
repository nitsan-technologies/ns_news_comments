<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$versionInformation = GeneralUtility::makeInstance(Typo3Version::class);
if ($versionInformation->getMajorVersion() < 12) {
    // @extensionScannerIgnoreLine
    ExtensionManagementUtility::addLLrefForTCAdescr('tx_nsnewscomments_domain_model_comment', 'EXT:ns_news_comments/Resources/Private/Language/locallang_csh_tx_newscomment_domain_model_comment.xlf');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_nsnewscomments_domain_model_comment');
}
