<?php
return [
    'tools_newt' => [
        'parent' => 'tools',
        'access' => 'user,group',
        'path' => '/module/tools/newt',
        'iconIdentifier' => 'newt-admin-module',
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
