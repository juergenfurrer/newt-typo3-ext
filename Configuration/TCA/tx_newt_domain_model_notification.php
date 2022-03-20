<?php

$languageFile = 'LLL:EXT:newt/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'title' => $languageFile . 'tx_newt_domain_model_notification',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title,message',
        'iconfile' => 'EXT:newt/Resources/Public/Icons/tx_newt_domain_model_notification.png'
    ],
    'types' => [
        '1' => ['showitem' => 'title, message, send_datetime, is_topic, hidden, '.
                                '--div--;' . $languageFile . 'tx_newt_domain_model_notification.be_recipient_tab, beusers, beusergroups,'.
                                '--div--;' . $languageFile . 'tx_newt_domain_model_notification.fe_recipient_tab, feusers, feusergroups,'.
                                '--div--;' . $languageFile . 'tx_newt_domain_model_notification.result_tab, result_datetime, result'],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => $languageFile . 'tx_newt_domain_model_notification.hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 1,
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

        'title' => [
            'exclude' => false,
            'label' => $languageFile . 'tx_newt_domain_model_notification.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
                'default' => ''
            ],
        ],
        'message' => [
            'exclude' => false,
            'label' => $languageFile . 'tx_newt_domain_model_notification.message',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim,required',
                'default' => ''
            ]
        ],
        'send_datetime' => [
            'exclude' => true,
            'label' => $languageFile . 'tx_newt_domain_model_notification.send_datetime',
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
        'result_datetime' => [
            'exclude' => true,
            'label' => $languageFile . 'tx_newt_domain_model_notification.result_datetime',
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
        'result' => [
            'exclude' => true,
            'label' => $languageFile . 'tx_newt_domain_model_notification.result',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'default' => '',
            ]
        ],
        'is_topic' => [
            'exclude' => true,
            'label' => $languageFile . 'tx_newt_domain_model_notification.is_topic',
            'onChange' => 'reload',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                    ]
                ],
            ],
        ],
        'beusers' => [
            'exclude' => true,
            'label' => $languageFile . 'tx_newt_domain_model_notification.beusers',
            'displayCond' => 'FIELD:is_topic:REQ:false',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'be_users',
                'MM' => 'tx_newt_notification_beusers_mm',
                'size' => 5,
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
        'beusergroups' => [
            'exclude' => true,
            'label' => $languageFile . 'tx_newt_domain_model_notification.beusergroups',
            'displayCond' => 'FIELD:is_topic:REQ:false',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'be_groups',
                'MM' => 'tx_newt_notification_backendgroups_mm',
                'size' => 5,
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
        'feusers' => [
            'exclude' => true,
            'label' => $languageFile . 'tx_newt_domain_model_notification.feusers',
            'displayCond' => 'FIELD:is_topic:REQ:false',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'fe_users',
                'MM' => 'tx_newt_notification_feusers_mm',
                'size' => 5,
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
        'feusergroups' => [
            'exclude' => true,
            'label' => $languageFile . 'tx_newt_domain_model_notification.feusergroups',
            'displayCond' => 'FIELD:is_topic:REQ:false',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'fe_groups',
                'MM' => 'tx_newt_notification_frontendgroups_mm',
                'size' => 5,
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
    ],
];
