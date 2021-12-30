<?php
defined('TYPO3') || die();

(static function () {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['Newt']['Implementation'][] = \Infonique\Newt\Newt\ExtNews::class;

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Newt',
        'Api',
        [\Infonique\Newt\Controller\ApiController::class => 'endpoints, create, read, update, delete'],
        [\Infonique\Newt\Controller\ApiController::class => 'endpoints, create, read, update, delete']
    );
})();
