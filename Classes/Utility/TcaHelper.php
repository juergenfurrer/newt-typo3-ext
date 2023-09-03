<?php

namespace Swisscode\Newt\Utility;

use Swisscode\Newt\Domain\Model\Endpoint;
use Swisscode\Newt\Domain\Repository\EndpointRepository;
use Swisscode\Newt\NewtApi\EndpointInterface;
use Swisscode\Newt\NewtApi\EndpointOptionsInterface;
use ReflectionClass;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***
 *
 * This file is part of the "Billboard Manager" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020 Jürgen Furrer <info@swisscode.sk>, SwissCode
 *
 ***/
/**
 * TcaHelper
 */
class TcaHelper
{
    private string $languageFile = 'LLL:EXT:newt/Resources/Private/Language/locallang_db.xlf:';

    /**
     * Returns a list of Newt-Classes (called as itemsProcFunc).
     *
     * @param array $configuration Current field configuration
     * @return void
     */
    public function getNewtClasses(array &$configuration)
    {
        $pleaseSelect = $GLOBALS['LANG']->sL($this->languageFile . 'tx_newt_domain_model_endpoint.endpoint_class.select');
        $configuration['items'][] = [$pleaseSelect, null];
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['Newt']['Implementation'] ?? [] as $className) {
            $configuration['items'][] = [$className, $className];
        }
    }

    /**
     * Returns a list of Method-Types (called as itemsProcFunc).
     *
     * @param array $configuration Current field configuration
     * @return void
     */
    public function getAvailableMethods(array &$configuration)
    {
        $refl = new ReflectionClass(\Swisscode\Newt\NewtApi\MethodType::class);

        $endpointUid = intval($configuration['row']['endpoint']);
        if ($endpointUid > 0) {
            /** @var EndpointRepository */
            $endpointRepository = GeneralUtility::makeInstance(EndpointRepository::class);
            /** @var Endpoint */
            $endpoint = $endpointRepository->findByUid($endpointUid);
            if ($endpoint && !empty($endpoint->getEndpointClass())) {
                /** @var EndpointInterface */
                $endpointClass = GeneralUtility::makeInstance($endpoint->getEndpointClass());
                if ($endpointClass) {
                    foreach ($endpointClass->getAvailableMethodTypes() as $method) {
                        foreach ($refl->getConstants() ?? [] as $key => $val) {
                            if ($method == $val) {
                                $configuration['items'][] = [$key, $val];
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Returns the needed options of the selected endpoint
     *
     * @param array $configuration
     * @return void
     */
    public function getAvailableOptions(array &$configuration)
    {
        $endpointUid = intval($configuration['row']['endpoint']);
        if ($endpointUid > 0) {
            /** @var EndpointRepository */
            $endpointRepository = GeneralUtility::makeInstance(EndpointRepository::class);
            /** @var Endpoint */
            $endpoint = $endpointRepository->findByUid($endpointUid);
            if ($endpoint && !empty($endpoint->getEndpointClass())) {
                /** @var EndpointOptionsInterface */
                $endpointClass = GeneralUtility::makeInstance($endpoint->getEndpointClass());
                if ($endpointClass instanceof EndpointOptionsInterface) {
                    foreach ($endpointClass->getNeededOptions() as $key => $val) {
                        $configuration['items'][] = [$key, $val];
                    }
                }
            }
        }
    }

    /**
     * Returns the title for options
     *
     * @param array $parameters
     * @return void
     */
    public function optionsTitle(&$parameters)
    {
        $record = BackendUtility::getRecord($parameters['table'], $parameters['row']['uid']);
        $newTitle = $record['option_name'] . ' = ' . $record['option_value'];
        $parameters['title'] = $newTitle;
    }
}
