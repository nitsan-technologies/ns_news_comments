<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

$versionInformation = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);
if ($versionInformation->getMajorVersion() < 12) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_nsnewscomments_domain_model_comment', 'EXT:ns_news_comments/Resources/Private/Language/locallang_csh_tx_newscomment_domain_model_comment.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_nsnewscomments_domain_model_comment');
}
