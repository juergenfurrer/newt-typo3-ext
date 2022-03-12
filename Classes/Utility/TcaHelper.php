<?php

namespace Infonique\Newt\Utility;

use Infonique\Newt\Domain\Model\Endpoint;
use Infonique\Newt\Domain\Repository\EndpointRepository;
use Infonique\Newt\NewtApi\EndpointInterface;
use ReflectionClass;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/***
 *
 * This file is part of the "Billboard Manager" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020 Jürgen Furrer <juergen@infonique.ch>, infonique, furrer
 *
 ***/
/**
 * TcaHelper
 */
class TcaHelper
{
    /**
     * Returns a list of Newt-Classes (called as itemsProcFunc).
     *
     * @param array $configuration Current field configuration
     * @return void
     */
    public function getNewtClasses(array &$configuration)
    {
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
    public function getAvailableMethods(array &$configuration, $a)
    {
        $refl = new ReflectionClass(\Infonique\Newt\NewtApi\MethodType::class);

        $endpointUid = intval($configuration['row']['endpoint']);
        if ($endpointUid > 0) {
            /** @var ObjectManager */
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            /** @var EndpointRepository */
            $endpointRepository = $objectManager->get(EndpointRepository::class);
            /** @var Endpoint */
            $endpoint = $endpointRepository->findByUid($endpointUid);
            if ($endpoint) {
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
}
