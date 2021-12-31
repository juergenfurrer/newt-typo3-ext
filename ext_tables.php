<?php
defined('TYPO3') || die();

(static function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Newt',
        'tools',
        'admin',
        '',
        [
            \Infonique\Newt\Controller\EndpointController::class => 'index, tokenRefresh'
        ],
        [
            'access' => 'user,group',
            'icon'   => 'EXT:newt/Resources/Public/Icons/user_mod_admin.svg',
            'labels' => 'LLL:EXT:newt/Resources/Private/Language/locallang_admin.xlf',
        ]
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_newt_domain_model_method');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_newt_domain_model_endpoint');
})();
