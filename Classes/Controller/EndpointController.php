<?php

declare(strict_types=1);

namespace Infonique\Newt\Controller;

use DateTimeZone;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Jürgen Furrer <juergen@infonique.ch>
 */

/**
 * EndpointController
 */
class EndpointController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * endpointRepository
     *
     * @var \Infonique\Newt\Domain\Repository\EndpointRepository
     */
    protected $endpointRepository = null;

    /**
     * @param \Infonique\Newt\Domain\Repository\EndpointRepository $endpointRepository
     */
    public function injectEndpointRepository(\Infonique\Newt\Domain\Repository\EndpointRepository $endpointRepository)
    {
        $this->endpointRepository = $endpointRepository;
    }


    /**
     * backendUserRepository
     *
     * @var \Infonique\Newt\Domain\Repository\BackendUserRepository
     */
    protected $backendUserRepository = null;

    /**
     * @param \Infonique\Newt\Domain\Repository\BackendUserRepository $backendUserRepository
     */
    public function injectBackenUserRepository(\Infonique\Newt\Domain\Repository\BackendUserRepository $backendUserRepository)
    {
        $this->backendUserRepository = $backendUserRepository;
    }

    /**
     * action index
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function indexAction(): \Psr\Http\Message\ResponseInterface
    {
        /** @var ObjectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var ConfigurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);
        $conf = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $settings = $conf['plugin.']['tx_newt.']['settings.'] ?? [];

        $uri = $this->uriBuilder->reset()
            ->setTargetPageUid(intval($settings['apiPageId'] ?? 1))
            ->setTargetPageType(intval($settings['apiTypeNum']))
            ->setCreateAbsoluteUri(true)
            ->buildFrontendUri();
        $apiBaseUrl = trim($settings['apiBaseUrl']);

        $data = [];
        $data["name"] = !empty($settings['apiName']) ? substr($settings['apiName'], 0, 25) : '';
        $data["user"] = $GLOBALS['BE_USER']->user['username'];
        $data["url"] = $apiBaseUrl . $uri;
        $userToken = $GLOBALS['BE_USER']->user['tx_newt_token'];
        $tokenIssed = $GLOBALS['BE_USER']->user['tx_newt_token_issued'];
        if (empty($userToken)) {
            $userToken = $this->backendUserRepository->updateBackendUserToken($GLOBALS['BE_USER']->user['uid']);
        }
        $data["token"] = $userToken;

        $timeZoneUtc = new DateTimeZone('UTC');
        $timeZoneDefault = date_default_timezone_get();
        $this->view->assign('timeZone', $timeZoneDefault);

        $tokenIssedDate = new \DateTime('@' . $tokenIssed, $timeZoneUtc);
        $tokenIssedDate->setTimezone(new \DateTimeZone($timeZoneDefault));
        $this->view->assign('tokenIssedDate', $tokenIssedDate);

        // Calculate if the token is expired
        if (intval($settings['tokenExpiration']) > 0) {
            $tokenExpireDate = new \DateTime('@' . $tokenIssed, $timeZoneUtc);
            $tokenExpireDate->setTimezone(new \DateTimeZone($timeZoneDefault));
            $tokenExpireDate->add(new \DateInterval("PT{$settings['tokenExpiration']}S"));
            $this->view->assign('tokenExpireDate', $tokenExpireDate);
            $data['expire'] = $tokenExpireDate->getTimestamp();

            $now = new \DateTime();
            if ($tokenExpireDate->getTimestamp() < $now->getTimestamp()) {
                $this->view->assign('tokenExpired', true);
            }
        }

        $endpoints = $this->endpointRepository->findAll();
        $this->view->assign('endpoints', $endpoints);

        $this->view->assign('tooken', $userToken);
        $this->view->assign('data', $data);
        $this->view->assign('qr_content', json_encode((object)$data));

        return $this->htmlResponse();
    }

    /**
     * action tokenRefresh
     *
     * @return void
     */
    public function tokenRefreshAction()
    {
        $this->backendUserRepository->updateBackendUserToken($GLOBALS['BE_USER']->user['uid']);
        $this->redirect("index");
    }
}
