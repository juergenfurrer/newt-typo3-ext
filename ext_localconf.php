<?php
defined('TYPO3') || die();

(static function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Newt',
        'Api',
        [\Infonique\Newt\Controller\ApiController::class => 'endpoints, create, read, update, delete'],
        [\Infonique\Newt\Controller\ApiController::class => 'endpoints, create, read, update, delete']
    );
})();
