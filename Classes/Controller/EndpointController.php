<?php

declare(strict_types=1);

namespace Infonique\Newt\Controller;

use DateTimeZone;
use Infonique\Newt\Domain\Model\Endpoint;
use Infonique\Newt\Domain\Model\Method;
use Infonique\Newt\Domain\Model\UserData;
use TYPO3\CMS\Core\Http\ApplicationType;
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
     * userRepository
     *
     * @var \Infonique\Newt\Domain\Repository\UserRepository
     */
    protected $userRepository = null;

    /**
     * @param \Infonique\Newt\Domain\Repository\UserRepository $userRepository
     */
    public function injectBackenUserRepository(\Infonique\Newt\Domain\Repository\UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * action index
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function indexAction()
    {
        if ($this->isFrontendRequest()) {
            // Request is from Frontend
            $userType = 'FE';
            $userUid = $GLOBALS['TSFE']->fe_user->user['uid'];
            $userName = $GLOBALS['TSFE']->fe_user->user['username'];
            $userGroups = GeneralUtility::intExplode(",", $GLOBALS['TSFE']->fe_user->user['usergroup']);
            $userToken = $GLOBALS['TSFE']->fe_user->user['tx_newt_token'];
            $tokenIssued = $GLOBALS['TSFE']->fe_user->user['tx_newt_token_issued'];
            $userIsAdmin = false;
        } else {
            // Request is from Backend
            $userType = 'BE';
            $userUid = $GLOBALS['BE_USER']->user['uid'];
            $userName = $GLOBALS['BE_USER']->user['username'];
            $userGroups = $GLOBALS['BE_USER']->userGroupsUID;
            $userToken = $GLOBALS['BE_USER']->user['tx_newt_token'];
            $tokenIssued = $GLOBALS['BE_USER']->user['tx_newt_token_issued'];
            $userIsAdmin = $GLOBALS['BE_USER']->user['admin'] > 0;
        }

        if (intval($userUid) > 0) {
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
            $apiBaseUrl = trim($settings['apiBaseUrl'] ?? '');

            $data = [];
            $data["name"] = !empty($settings['apiName']) ? substr($settings['apiName'], 0, 25) : '';
            $data["user"] = $userName;
            $data["url"] = $apiBaseUrl . $uri;
            if (empty($userToken)) {
                if ($userType == 'BE') {
                    $userToken = $this->userRepository->updateBackendUserToken($userUid);
                } else {
                    $userToken = $this->userRepository->updateFrontendUserToken($userUid);
                }
                $now = new \DateTime();
                $tokenIssued = $now->getTimestamp();
            }
            $data["token"] = $userToken;

            $timeZoneUtc = new DateTimeZone('UTC');
            $timeZoneDefault = date_default_timezone_get();
            $this->view->assign('timeZone', $timeZoneDefault);

            $tokenIssuedDate = new \DateTime('@' . $tokenIssued, $timeZoneUtc);
            $tokenIssuedDate->setTimezone(new \DateTimeZone($timeZoneDefault));
            $this->view->assign('tokenIssuedDate', $tokenIssuedDate);

            // Calculate if the token is expired
            if (intval($settings['tokenExpiration']) > 0) {
                $tokenExpireDate = new \DateTime('@' . $tokenIssued, $timeZoneUtc);
                $tokenExpireDate->setTimezone(new \DateTimeZone($timeZoneDefault));
                $tokenExpireDate->add(new \DateInterval("PT{$settings['tokenExpiration']}S"));
                $this->view->assign('tokenExpireDate', $tokenExpireDate);
                $data['expire'] = $tokenExpireDate->getTimestamp();

                $now = new \DateTime();
                if ($tokenExpireDate->getTimestamp() < $now->getTimestamp()) {
                    $this->view->assign('tokenExpired', true);
                }
            }

            $listEndpoint = [];
            $endpoints = $this->endpointRepository->findAll();
            /** @var Endpoint $endpoint */
            foreach ($endpoints as $endpoint) {
                $className = $endpoint->getEndpointClass();
                $classExists = false;
                $methods = [];
                try {
                    if (class_exists($className, true)) {
                        /** @var Method $method */
                        foreach ($endpoint->getMethods() as $method) {
                            $userData = new UserData();
                            $userData->setType($userType);
                            $userData->setUid($userUid);
                            $userData->setUsergroups($userGroups);
                            $userData->setIsAdmin($userIsAdmin);
                            if ($method->isUserAllowed($userData)) {
                                $methods[] = $method->getType();
                            }
                        }
                        $classExists = true;
                    }
                } catch (\Exception $e) {
                    $classExists = false;
                }
                if ($classExists && count($methods) > 0) {
                    $listEndpoint[] = $endpoint;
                }
            }
            $this->view->assign('endpoints', $listEndpoint);

            $this->view->assign('tooken', $userToken);
            $this->view->assign('data', $data);
            $this->view->assign('qr_content', json_encode((object)$data));
        }

        if (method_exists($this, "htmlResponse")) {
            return $this->htmlResponse();
        }
    }

    /**
     * action tokenRefresh
     *
     * @return void
     */
    public function tokenRefreshAction()
    {
        if ($this->isFrontendRequest()) {
            // Request is from Frontend
            $this->userRepository->updateFrontendUserToken($GLOBALS['TSFE']->fe_user->user['uid']);
        } else {
            // Request is from Backend
            $this->userRepository->updateBackendUserToken($GLOBALS['BE_USER']->user['uid']);
        }
        $this->redirect("index");
    }

    /**
     * return true, if request is from frontend
     *
     * @return boolean
     */
    private function isFrontendRequest(): bool
    {
        if (is_subclass_of($this->request, '\\Psr\\Http\\Message\\ServerRequestInterface')) {
            return ApplicationType::fromRequest($this->request)->isFrontend();
        } else {
            return ! empty($GLOBALS['TSFE']);
        }
    }
}
