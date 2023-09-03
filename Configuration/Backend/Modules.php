<?php
return [
    'tools_NewtAdmin' => [
        'parent' => 'tools',
        'access' => 'admin,user',
        'iconIdentifier' => 'newt-admin-module',
        'path' => '/module/web/NewtAdmin',
        'labels' => 'LLL:EXT:newt/Resources/Private/Language/locallang_admin.xlf',
        'extensionName' => 'Newt',
        'controllerActions' => [
            \Swisscode\Newt\Controller\EndpointController::class => [
                'index',
                'tokenRefresh',
            ],
        ],
    ],
];
