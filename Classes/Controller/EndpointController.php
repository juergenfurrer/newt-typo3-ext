<?php

declare(strict_types=1);

namespace Infonique\Newt\Controller;

use TYPO3\CMS\Core\Database\ConnectionPool;
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
     * action list
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
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
        $data["user"] = $GLOBALS['BE_USER']->user['username'];
        $data["url"] = $apiBaseUrl . $uri;
        $userToken = $GLOBALS['BE_USER']->user['tx_newt_token'];
        if (empty($userToken)) {
            $userToken = $this->backendUserRepository->updateBackendUserToken($GLOBALS['BE_USER']->user['uid']);
        }
        $data["token"] = $userToken;
        $this->view->assign('qr_content', json_encode((object)$data));

        $endpoints = $this->endpointRepository->findAll();
        $this->view->assign('endpoints', $endpoints);
        $this->view->assign('tooken', $userToken);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \Infonique\Newt\Domain\Model\Endpoint $endpoint
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(\Infonique\Newt\Domain\Model\Endpoint $endpoint): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('endpoint', $endpoint);
        return $this->htmlResponse();
    }

    /**
     * action new
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function newAction(): \Psr\Http\Message\ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * action create
     *
     * @param \Infonique\Newt\Domain\Model\Endpoint $newEndpoint
     */
    public function createAction(\Infonique\Newt\Domain\Model\Endpoint $newEndpoint)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->endpointRepository->add($newEndpoint);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \Infonique\Newt\Domain\Model\Endpoint $endpoint
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("endpoint")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editAction(\Infonique\Newt\Domain\Model\Endpoint $endpoint): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('endpoint', $endpoint);
        return $this->htmlResponse();
    }
}
