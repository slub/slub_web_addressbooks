<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.SlubWebAddressbooks',
    'Booksearch',
    'SLUB: Adressbücher: Bücher'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.SlubWebAddressbooks',
    'Personsearch',
    'SLUB: Adressbücher: Personen'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.SlubWebAddressbooks',
    'Placesearch',
    'SLUB: Adressbücher: Orte'
);


$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['slubwebaddressbooks_placesearch'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'slubwebaddressbooks_placesearch',
    'FILE:EXT:slub_web_addressbooks/Configuration/FlexForms/flexform_placesearch.xml'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['slubwebaddressbooks_booksearch'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'slubwebaddressbooks_booksearch',
    'FILE:EXT:slub_web_addressbooks/Configuration/FlexForms/flexform_booksearch.xml'
);
