<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_slubwebaddressbooks_domain_model_book', 'EXT:slub_web_addressbooks/Resources/Private/Language/locallang_csh_tx_slubwebaddressbooks_domain_model_book.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_slubwebaddressbooks_domain_model_book');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_slubwebaddressbooks_domain_model_place', 'EXT:slub_web_addressbooks/Resources/Private/Language/locallang_csh_tx_slubwebaddressbooks_domain_model_place.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_slubwebaddressbooks_domain_model_place');
