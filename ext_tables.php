	<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_nsnewscomments_domain_model_comment', 'EXT:ns_news_comments/Resources/Private/Language/locallang_csh_tx_newscomment_domain_model_comment.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_nsnewscomments_domain_model_comment');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:ns_news_comments/Configuration/TSconfig/ContentElementWizard.tsconfig">'
);
