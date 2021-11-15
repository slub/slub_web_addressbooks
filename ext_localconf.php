<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Slub.' . $_EXTKEY,
	'Booksearch',
	array(
		'Book' => 'timeline, search, list, searchcombined',
	),
	// non-cacheable actions
	array(
		'Book' => 'checkdlf, search',
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Slub.' . $_EXTKEY,
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
	'Slub.' . $_EXTKEY,
	'Placesearch',
	array(
		'Place' => 'list, geojson',
	),
	// non-cacheable actions
	array(
	)
);

/*
 * Backend context
 */
if (TYPO3_MODE === 'BE') {

    // include cli command controller
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
        Slub\SlubWebAddressbooks\Command\ImportCommandController::class;

}
