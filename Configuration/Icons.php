<?php

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'newt-admin-module' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:newt/Resources/Public/Icons/user_mod_admin.svg',
    ],
    'newt-plugin-config' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:newt/Resources/Public/Icons/user_plugin_config.svg'
    ],
];
