<?php
defined('TYPO3') || die();

call_user_func(
    function ($extKey) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Newt',
            'Api',
            [\Swisscode\Newt\Controller\ApiController::class => 'endpoints, create, read, update, delete, list'],
            [\Swisscode\Newt\Controller\ApiController::class => 'endpoints, create, read, update, delete, list']
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Newt',
            'Serverconfig',
            [\Swisscode\Newt\Controller\EndpointController::class => 'index, tokenRefresh'],
            [\Swisscode\Newt\Controller\EndpointController::class => 'index, tokenRefresh']
        );

        // wizards
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            'mod {
                wizards.newContentElement.wizardItems.plugins {
                    elements {
                        serverconfig {
                            iconIdentifier = newt-plugin-serverconfig
                            title = LLL:EXT:newt/Resources/Private/Language/locallang_db.xlf:tx_newt_serverconfig.name
                            description = LLL:EXT:newt/Resources/Private/Language/locallang_db.xlf:tx_newt_serverconfig.description
                            tt_content_defValues {
                                CType = list
                                list_type = newt_serverconfig
                            }
                        }
                    }
                    show = *
                }
            }'
        );

        // Nodes
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['1647092550'] = [
            'nodeName' => 'NewtEndpointHintElement',
            'priority' => 40,
            'class' => \Swisscode\Newt\Form\Element\NewtEndpointHintElement::class,
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['1649659670'] = [
            'nodeName' => 'NewtEndpointOptionsHintElement',
            'priority' => 40,
            'class' => \Swisscode\Newt\Form\Element\NewtEndpointOptionsHintElement::class,
        ];

        // Tasks
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Swisscode\Newt\Task\SendNotificationTask::class] = array(
            'extension' => $extKey,
            'title' => 'LLL:EXT:newt/Resources/Private/Language/locallang_db.xlf:tx_scheduler.send_notification_task.name',
            'description' => 'LLL:EXT:newt/Resources/Private/Language/locallang_db.xlf:tx_scheduler.send_notification_task.description'
        );

        // LOG
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['Swisscode']['Newt']['Controller']['writerConfiguration'][\TYPO3\CMS\Core\Log\LogLevel::DEBUG] = [
            \TYPO3\CMS\Core\Log\Writer\DatabaseWriter::class => [
                'logTable' => 'tx_newt_log'
            ],
        ];

        /** @var \TYPO3\CMS\Core\Imaging\IconRegistry */
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'newt-plugin-serverconfig',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:newt/Resources/Public/Icons/user_plugin_serverconfig.svg']
        );
    },
    'newt'
);
