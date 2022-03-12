<?php
defined('TYPO3') || die();

call_user_func(
    function ($extKey) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Newt',
            'Api',
            [\Infonique\Newt\Controller\ApiController::class => 'endpoints, create, read, update, delete, list'],
            [\Infonique\Newt\Controller\ApiController::class => 'endpoints, create, read, update, delete, list']
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Newt',
            'Serverconfig',
            [\Infonique\Newt\Controller\EndpointController::class => 'index, tokenRefresh'],
            [\Infonique\Newt\Controller\EndpointController::class => 'index, tokenRefresh']
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
            'class' => \Infonique\Newt\Form\Element\NewtEndpointHintElement::class,
        ];

        /** @var \TYPO3\CMS\Core\Imaging\IconRegistry */
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'newt-plugin-serverconfig',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:newt/Resources/Public/Icons/user_plugin_serverconfig.svg']
        );
    },
    $_EXTKEY ?? 'newt'
);
