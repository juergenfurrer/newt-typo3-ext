<?php
defined('TYPO3') || die();

(static function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Newt',
        'Api',
        [\Infonique\Newt\Controller\ApiController::class => 'endpoints, create, read, update, delete, list'],
        [\Infonique\Newt\Controller\ApiController::class => 'endpoints, create, read, update, delete, list']
    );
})();
