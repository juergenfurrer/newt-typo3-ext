<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Newt',
    'description' => 'Backend-Extension to manage the Newt-Access',
    'category' => 'module',
    'author' => 'infonique, furrer',
    'author_email' => 'info@infonique.ch',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '2.2.2',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
