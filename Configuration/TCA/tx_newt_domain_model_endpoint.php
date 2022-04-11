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
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'name,description,endpoint_class,option1,option2,option3',
        'iconfile' => 'EXT:newt/Resources/Public/Icons/tx_newt_domain_model_endpoint.png'
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
           'showitem' => 'option1, option2, option3, options_hint',
        ],
     ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_newt_domain_model_endpoint',
                'foreign_table_where' => 'AND {#tx_newt_domain_model_endpoint}.{#pid}=###CURRENT_PID### AND {#tx_newt_domain_model_endpoint}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
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
                'eval' => 'trim',
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
                'itemsProcFunc' => \Infonique\Newt\Utility\TcaHelper::class . '->getNewtClasses',
            ],
        ],
        'endpoint_hint' => [
            'exclude' => FALSE,
            'config' => [
                'type' => 'user',
                'renderType' => 'NewtEndpointHintElement'
            ]
        ],
        'option1' => [
            'exclude' => true,
            'label' => $languageFile . 'tx_newt_domain_model_endpoint.option1',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'option2' => [
            'exclude' => true,
            'label' => $languageFile . 'tx_newt_domain_model_endpoint.option2',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'option3' => [
            'exclude' => true,
            'label' => $languageFile . 'tx_newt_domain_model_endpoint.option3',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'options_hint' => [
            'exclude' => FALSE,
            'config' => [
                'type' => 'user',
                'renderType' => 'NewtEndpointOptionsHintElement'
            ]
        ],
        'page_uid' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
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
                'maxitems' => 9999,
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
