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
		'Book' => 'search',
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
