<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Newt',
    'description' => 'Backend-Extension to manage the Newt-Access',
    'category' => 'module',
    'author' => 'Jürgen Furrer',
    'author_email' => 'juergen@infonique.ch',
    'state' => 'alpha',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
