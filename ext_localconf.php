<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Slub.SlubWebAddressbooks',
	'Booksearch',
	array(
		'Book' => 'timeline, search, list, searchcombined',
	),
	// non-cacheable actions
	array(
		'Book' => 'search, searchcombined',
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Slub.SlubWebAddressbooks',
	'Personsearch',
	array(
		'Person' => 'ajax',
	),
	// non-cacheable actions
	array(
		'Person' => 'ajax',
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Slub.SlubWebAddressbooks',
	'Placesearch',
	array(
		'Place' => 'list',
	),
	// non-cacheable actions
	array(
	)
);

/**
 * Add default RTE configuration
 */
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['slub_web_addressbooks'] = 'EXT:slub_web_addressbooks/Configuration/RTE/Default.yaml';
