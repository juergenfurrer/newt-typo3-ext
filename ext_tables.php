<?php

use Swisscode\Newt\Utility\Utils;

defined('TYPO3') || die();

(static function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Newt',
        'Serverconfig',
        'Server-Config'
    );

    if (!Utils::isVersion12() && TYPO3_MODE === 'BE' && !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_CLI)) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'Newt',
            'tools',
            'admin',
            '',
            [
                \Swisscode\Newt\Controller\EndpointController::class => 'index, tokenRefresh'
            ],
            [
                'access' => 'admin,user',
                'icon'   => 'EXT:newt/Resources/Public/Icons/user_mod_admin.svg',
                'labels' => 'LLL:EXT:newt/Resources/Private/Language/locallang_admin.xlf',
            ]
        );
    }
})();
