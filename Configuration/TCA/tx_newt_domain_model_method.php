<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:newt/Resources/Private/Language/locallang_db.xlf:tx_newt_domain_model_method',
        'label' => 'type',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'delete' => 'deleted',
        'hideTable' => true,
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'type',
        'iconfile' => 'EXT:newt/Resources/Public/Icons/tx_newt_domain_model_method.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'type, users, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
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

        'type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:newt/Resources/Private/Language/locallang_db.xlf:tx_newt_domain_model_method.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1,
                'itemsProcFunc' => \Infonique\Newt\Utility\TcaHelper::class . '->getAvailableMethods',
            ],
        ],
        'users' => [
            'exclude' => true,
            'label' => 'LLL:EXT:newt/Resources/Private/Language/locallang_db.xlf:tx_newt_domain_model_method.users',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'be_users',
                'MM' => 'tx_newt_method_backenduser_mm',
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],

        ],

        'endpoint' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
