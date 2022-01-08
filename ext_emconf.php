<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Newt',
    'description' => 'Backend-Extension to manage the Newt-Access',
    'category' => 'module',
    'author' => 'infonique, furrer',
    'author_email' => 'info@infonique.ch',
    'state' => 'alpha',
    'clearCacheOnLoad' => 0,
    'version' => '1.3.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
