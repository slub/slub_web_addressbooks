<?php
return array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book',
		'label' => 'year_string',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'versioningWS' => true,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'year_string,year,ppn,page_behoerdenverzeichnis,page_berufsklassen_und_gewerbe,page_handelsregister,page_genossenschaftsregister,link_map,link_map_thumb,order_umlaute,order_i_j,place_id,',
		'iconfile' => 'EXT:slub_web_addressbooks/Resources/Public/Icons/tx_slubwebaddressbooks_domain_model_book.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, year_string, year, ppn, persons, streets, page_behoerdenverzeichnis, page_berufsklassen_und_gewerbe, page_handelsregister, page_genossenschaftsregister, link_map, link_map_thumb, order_umlaute, order_i_j, place_id',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, year_string, year, ppn, persons, streets, page_behoerdenverzeichnis, page_berufsklassen_und_gewerbe, page_handelsregister, page_genossenschaftsregister, link_map, link_map_thumb, order_umlaute, order_i_j, place_id,--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_slubwebaddressbooks_domain_model_book',
				'foreign_table_where' => 'AND tx_slubwebaddressbooks_domain_model_book.pid=###CURRENT_PID### AND tx_slubwebaddressbooks_domain_model_book.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'year_string' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book.year_string',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'year' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book.year',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int,required'
			),
		),
		'ppn' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book.ppn',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'persons' => [
				'exclude'       => 0,
				'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book.persons',
				'config'        => [
						'type'    => 'text',
						'cols'    => 40,
						'rows'    => 40,
						'eval'    => 'trim',
				],
		],
		'streets' => [
				'exclude'       => 0,
				'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book.streets',
				'config'        => [
						'type'    => 'text',
						'cols'    => 40,
						'rows'    => 40,
						'eval'    => 'trim',
				],
		],
		'page_behoerdenverzeichnis' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book.page_behoerdenverzeichnis',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			),
		),
		'page_berufsklassen_und_gewerbe' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book.page_berufsklassen_und_gewerbe',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			),
		),
		'page_handelsregister' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book.page_handelsregister',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			),
		),
		'page_genossenschaftsregister' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book.page_genossenschaftsregister',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			),
		),
		'link_map' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book.link_map',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'link_map_thumb' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book.link_map_thumb',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'order_umlaute' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book.order_umlaute',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'order_i_j' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book.order_i_j',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'place_id' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_book.place_id',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_slubwebaddressbooks_domain_model_place',
				'minitems' => 0,
				'maxitems' => 1,
			),
		),
	),
);
