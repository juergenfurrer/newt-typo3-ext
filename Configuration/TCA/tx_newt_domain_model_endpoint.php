<?php

$languageFile = 'LLL:EXT:newt/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'title' => $languageFile . 'tx_newt_domain_model_endpoint',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'versioningWS' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'name,description,endpoint_class',
        'iconfile' => 'EXT:newt/Resources/Public/Icons/tx_newt_domain_model_endpoint.png',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'name, description, --palette--;;endpointPalette, --palette--;;optionsPalette, page_uid, methods, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
    ],
    'palettes' => [
        'endpointPalette' => [
            'label' => '',
            'showitem' => 'endpoint_class, endpoint_hint',
        ],
        'optionsPalette' => [
            'label' => '',
            'showitem' => 'options, options_hint',
        ],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],

        'name' => [
            'exclude' => false,
            'label' => $languageFile . 'tx_newt_domain_model_endpoint.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
                'default' => ''
            ],
        ],
        'description' => [
            'exclude' => false,
            'label' => $languageFile . 'tx_newt_domain_model_endpoint.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'default' => ''
            ]
        ],
        'endpoint_class' => [
            'exclude' => true,
            'label' => $languageFile . 'tx_newt_domain_model_endpoint.endpoint_class',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1,
                'itemsProcFunc' => \Swisscode\Newt\Utility\TcaHelper::class . '->getNewtClasses',
            ],
        ],
        'endpoint_hint' => [
            'exclude' => false,
            'config' => [
                'type' => 'user',
                'renderType' => 'NewtEndpointHintElement'
            ]
        ],
        'options' => [
            'exclude' => false,
            'label' => $languageFile . 'tx_newt_domain_model_endpoint.options',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_newt_domain_model_endpointoption',
                'foreign_field' => 'endpoint',
                'maxitems' => 99,
                'appearance' => [
                    'collapseAll' => 1,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],
        ],
        'options_hint' => [
            'exclude' => false,
            'config' => [
                'type' => 'user',
                'renderType' => 'NewtEndpointOptionsHintElement'
            ]
        ],
        'page_uid' => [
            'exclude' => false,
            'label' => $languageFile . 'tx_newt_domain_model_endpoint.page_uid',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectTree',
                'foreign_table' => 'pages',
                'foreign_table_where' => 'ORDER BY pages.sorting',
                'items' => [],
                'size' => 10,
                'treeConfig' => [
                    'parentField' => 'pid',
                    'appearance' => [
                        'expandAll' => TRUE,
                        'showHeader' => TRUE
                    ]
                ],
                'minitems' => 0,
                'maxitems' => 1
            ],
        ],
        'methods' => [
            'exclude' => false,
            'label' => $languageFile . 'tx_newt_domain_model_endpoint.methods',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_newt_domain_model_method',
                'foreign_field' => 'endpoint',
                'maxitems' => 99,
                'appearance' => [
                    'collapseAll' => 0,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],
        ],
    ],
];
