<?php
return array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_place',
		'label' => 'place',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'place,lat,lon,hov_link,gndid,',
		'iconfile' => 'EXT:slub_web_addressbooks/Resources/Public/Icons/tx_slubwebaddressbooks_domain_model_place.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'hidden, place, lat, lon, hov_link, gndid',
	),
	'types' => array(
		'1' => array('showitem' => 'hidden, place, lat, lon, hov_link, gndid,--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
		'tstamp' => [
		  'label' => 'tstamp',
		  'config' => [
		   'type' => 'passthrough',
		  ]
		],
        'starttime'                => [
            'exclude'   => 1,
            'l10n_mode' => 'exclude',
            'label'     => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config'    => [
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ],
                'type'     => 'input',
                'renderType' => 'inputDateTime',
                'size'     => 13,
                'eval'     => 'datetime',
                'default'  => 0,
            ],
        ],
        'endtime'                  => [
            'exclude'   => 1,
            'l10n_mode' => 'exclude',
            'label'     => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config'    => [
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ],
                'type'     => 'input',
                'renderType' => 'inputDateTime',
                'size'     => 13,
                'eval'     => 'datetime',
                'default'  => 0,
            ],
        ],
    	'place' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_place.place',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'lat' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_place.lat',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'lon' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_place.lon',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'hov_link' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_place.hov_link',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'gndid' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang_db.xlf:tx_slubwebaddressbooks_domain_model_place.gndid',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
	),
);
