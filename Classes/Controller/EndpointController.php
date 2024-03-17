<?php

declare(strict_types=1);

namespace Swisscode\Newt\Controller;

use DateTimeZone;
use Swisscode\Newt\Domain\Model\Endpoint;
use Swisscode\Newt\Domain\Model\Method;
use Swisscode\Newt\Domain\Model\UserData;
use Swisscode\Newt\Utility\Utils;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Jürgen Furrer <info@swisscode.sk>
 */

/**
 * EndpointController
 */
class EndpointController extends BaseController
{

    /**
     * endpointRepository
     *
     * @var \Swisscode\Newt\Domain\Repository\EndpointRepository
     */
    protected $endpointRepository = null;
    /**
     * @param \Swisscode\Newt\Domain\Repository\EndpointRepository $endpointRepository
     */
    public function injectEndpointRepository(\Swisscode\Newt\Domain\Repository\EndpointRepository $endpointRepository)
    {
        $this->endpointRepository = $endpointRepository;
        $this->endpointRepository->setDetachedQuerySettings();
    }

    /**
     * userRepository
     *
     * @var \Swisscode\Newt\Domain\Repository\UserRepository
     */
    protected $userRepository = null;
    /**
     * @param \Swisscode\Newt\Domain\Repository\UserRepository $userRepository
     */
    public function injectBackenUserRepository(\Swisscode\Newt\Domain\Repository\UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * moduleTemplateFactory
     *
     * @var ModuleTemplateFactory
     */
    protected ModuleTemplateFactory $moduleTemplateFactory;
    /**
     * @param ModuleTemplateFactory $moduleTemplateFactory
     */
    public function injectModuleTemplateFactory(ModuleTemplateFactory $moduleTemplateFactory)
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    /**
     * action index
     *
     * @return ResponseInterface
     */
    public function indexAction(): ResponseInterface
    {
        /** @var ConfigurationManager */
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $conf = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $settings = $conf['plugin.']['tx_newt.']['settings.'] ?? [];

        $isfrontend = $this->isFrontendRequest();
        $viewData = [];
        $layout = "Default";
        if ($isfrontend) {
            // Request is from Frontend
            $userType = 'FE';
            $userUid = intval($this->getArrayKeyValue($GLOBALS['TSFE']->fe_user->user, 'uid'));
            if ($userUid > 0) {
                $userName = $settings['feuserNamePrefix'] . $this->getArrayKeyValue($GLOBALS['TSFE']->fe_user->user, 'username');
                $userGroups = GeneralUtility::intExplode(",", $this->getArrayKeyValue($GLOBALS['TSFE']->fe_user->user, 'usergroup'));
                $userToken = $this->getArrayKeyValue($GLOBALS['TSFE']->fe_user->user, 'tx_newt_token');
                $tokenIssued = $this->getArrayKeyValue($GLOBALS['TSFE']->fe_user->user, 'tx_newt_token_issued');
                $userIsAdmin = false;
            }
        } else {
            // Request is from Backend
            $userType = 'BE';
            $userUid = intval($this->getArrayKeyValue($GLOBALS['BE_USER']->user, 'uid'));
            if ($userUid > 0) {
                $userName = $this->getArrayKeyValue($GLOBALS['BE_USER']->user, 'username');
                $userGroups = $GLOBALS['BE_USER']->userGroupsUID;
                $userToken = $this->getArrayKeyValue($GLOBALS['BE_USER']->user, 'tx_newt_token');
                $tokenIssued = $this->getArrayKeyValue($GLOBALS['BE_USER']->user, 'tx_newt_token_issued');
                $userIsAdmin = intval($this->getArrayKeyValue($GLOBALS['BE_USER']->user, 'admin')) > 0;
                $layout = "Module";
            }
        }

        if ($userUid > 0) {
            $data = [];
            $data["name"] = !empty($settings['apiName']) ? substr($settings['apiName'], 0, 25) : '';
            $data["user"] = $userName;
            $data["url"] = Utils::getApiUrl($this->uriBuilder);
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
            $viewData['timeZone'] = $timeZoneDefault;

            $tokenIssuedDate = new \DateTime('@' . $tokenIssued, $timeZoneUtc);
            $tokenIssuedDate->setTimezone(new \DateTimeZone($timeZoneDefault));
            $viewData['tokenIssuedDate'] = $tokenIssuedDate;

            // Calculate if the token is expired
            if (intval($settings['tokenExpiration']) > 0) {
                $tokenExpireDate = new \DateTime('@' . $tokenIssued, $timeZoneUtc);
                $tokenExpireDate->setTimezone(new \DateTimeZone($timeZoneDefault));
                $tokenExpireDate->add(new \DateInterval("PT{$settings['tokenExpiration']}S"));
                $viewData['tokenExpireDate'] = $tokenExpireDate;
                $data['expire'] = $tokenExpireDate->getTimestamp();

                $now = new \DateTime();
                if ($tokenExpireDate->getTimestamp() < $now->getTimestamp()) {
                    $viewData['tokenExpired'] = true;
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
            $viewData['endpoints'] = $listEndpoint;

            $viewData['tooken'] = $userToken;
            $viewData['data'] = $data;
            $viewData['qr_content'] = json_encode((object)$data);
        }

        $viewData['layout'] = $layout;

        if ($isfrontend) {
            $this->view->assignMultiple($viewData);
            return $this->htmlResponse();
        } else if (Utils::isVersion12Plus()) {
            $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
            $moduleTemplate->assignMultiple($viewData);
            return $moduleTemplate->renderResponse();
        } else {
            $viewData['layoutSuffix'] = "Old";
            $this->view->assignMultiple($viewData);
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
            $this->userRepository->updateFrontendUserToken($this->getArrayKeyValue($GLOBALS['TSFE']->fe_user->user, 'uid'));
        } else {
            // Request is from Backend
            $this->userRepository->updateBackendUserToken($this->getArrayKeyValue($GLOBALS['BE_USER']->user, 'uid'));
        }

        return $this->redirect("index");
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
