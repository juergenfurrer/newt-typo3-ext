<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Newt',
    'description' => 'Backend-Extension to manage the Newt-Access',
    'category' => 'module',
    'author' => 'swissCode',
    'author_email' => 'info@swisscode.sk',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '3.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
