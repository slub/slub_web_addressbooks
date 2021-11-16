<?php
defined('TYPO3_MODE') || die();

call_user_func(function()
{
    /**
     * Default PageTS for Historical Addressbooks
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile (
        'slub_web_addressbooks',
        'Configuration/TsConfig/setup.tsconfig',
        'Historische Adressbücher: Page TS'
    );

    // there should be no '/' in slug path. Replace by empty string is same behaviour as in RealUrl
    $GLOBALS['TCA']['pages']['columns']['slug']['config']['generatorOptions']['replacements'] = [
        '/' => ''
    ];
});
