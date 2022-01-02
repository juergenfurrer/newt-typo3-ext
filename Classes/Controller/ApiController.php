<?php

declare(strict_types=1);

namespace Infonique\Newt\Controller;

use Infonique\Newt\Domain\Model\Endpoint;
use Infonique\Newt\NewtApi\Field;
use Infonique\Newt\NewtApi\FieldType;
use Infonique\Newt\NewtApi\MethodCreateModel;
use Infonique\Newt\NewtApi\MethodType;
use Infonique\Newt\NewtApi\Response;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Jürgen Furrer <juergen@infonique.ch>
 */

/**
 * ApiController
 */
class ApiController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var \TYPO3\CMS\Extbase\Mvc\View\JsonView
     */
    protected $view;

    /**
     * @var string
     */
    protected $defaultViewObjectName = \TYPO3\CMS\Extbase\Mvc\View\JsonView::class;

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
     * Returns the endpoints of this server
     */
    public function endpointsAction()
    {
        $userData = $this->backendUserRepository->findUserDataByRequest($this->request);
        $userUid = $this->validateUser($userData);
        if ($userUid > 0) {
            $endpoints = $this->endpointRepository->findAll();
            $json = [];
            /** @var Endpoint $endpoint */
            foreach ($endpoints as $endpoint) {
                $data = $endpoint->getData($userUid, $this->settings);
                if ($data) {
                    $json[] = $data;
                }
            }
            $this->view->assign('endpoints', $json);
            $this->view->setVariablesToRender(array(
                'endpoints'
            ));

            $this->view->setConfiguration([
                'endpoints' => [
                    '_descendAll' => [
                        '_descend' => [
                            'configuration' => [
                                '_descend' => [
                                    'fields' => [
                                        '_descendAll' => [
                                            '_descend' => [
                                                'validation' => [
                                                    '_descendAll' => [],
                                                ],
                                                'items' => [
                                                    '_descendAll' => [],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'methods' => [
                                        '_descendAll' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     * action create
     */
    public function createAction()
    {
        $response = new Response();
        $result = false;

        $userData = $this->backendUserRepository->findUserDataByRequest($this->request);
        $userUid = $this->validateUser($userData);
        if ($userUid > 0) {
            $endpointUid = 0;
            if ($this->request->hasArgument('uid')) {
                $endpointUid = $this->request->getArgument('uid');
            }
            /** @var Endpoint */
            $endpoint = $this->endpointRepository->findByUid(intval($endpointUid));
            if (!$endpoint) {
                $response->setError(404, "Endpoint not found");
            } else {
                $currentMethod = $endpoint->getMethodByType(MethodType::CREATE);
                if (! $currentMethod || ! $currentMethod->isUserAllowed($userUid)) {
                    $response->setError(403, "User not allowed");
                } else {
                    $className = $endpoint->getEndpointClass();
                    if (!class_exists($className)) {
                        $response->setError(404, "EndpointClass not found");
                    } else {
                        /** @var \Infonique\Newt\NewtApi\EndpointInterface */
                        $endpointImplementation = new $className();
                        $prams = [];
                        $isValid = true;
                        /** @var Field $field */
                        foreach ($endpointImplementation->getAvailableFields() as $field) {
                            $fieldName = $field->getName();
                            if (isset($_POST[$fieldName])) {
                                if ($field->getType() == FieldType::CHECKBOX) {
                                    $prams[$fieldName] = boolval($_POST[$fieldName]);
                                } else if ($field->getType() == FieldType::DATETIME) {
                                    $prams[$fieldName] = new \DateTime($_POST[$fieldName]);
                                } else if ($field->getType() == FieldType::DATE) {
                                    $prams[$fieldName] = new \DateTime($_POST[$fieldName]);
                                } else if ($field->getType() == FieldType::TIME) {
                                    $prams[$fieldName] = new \DateTime("1900-01-01 " . $_POST[$fieldName]);
                                } else if ($field->getType() == FieldType::IMAGE) {
                                    if (strlen($_POST[$fieldName]) > 0) {
                                        $prams[$fieldName] = $this->setImageFromBase64(md5($_POST[$fieldName]).".jpg", $_POST[$fieldName], "be_user_" . $userUid);
                                    }
                                } else {
                                    $prams[$fieldName] = $_POST[$fieldName];
                                }
                            }
                            $validation = $field->getValidation();
                            if ($validation != null) {
                                if ($validation->getRequired() && empty($prams[$fieldName])) {
                                    $isValid = false;
                                }
                            }
                        }
                        if (! $isValid) {
                            $response->setError(400, "Form not valid");
                        } else {
                            $methodCreateModel = new MethodCreateModel();
                            $methodCreateModel->setBackendUserUid($userUid);
                            $methodCreateModel->setParams($prams);
                            $methodCreateModel->setPageUid($endpoint->getPageUid());
                            $result = $endpointImplementation->methodCreate($methodCreateModel);
                        }
                    }
                }
            }
            $response->setSuccess($result);

            $this->view->assign("json", $response->getJson());
            $this->view->setVariablesToRender([
                "json"
            ]);
        }
    }

    /**
     * action read
     */
    public function readAction()
    {
        $this->view->assign('json', []);
        $this->view->setVariablesToRender(array(
            'json'
        ));
    }

    /**
     * action update
     */
    public function updateAction()
    {
        $this->view->assign('json', false);
        $this->view->setVariablesToRender(array(
            'json'
        ));
    }

    /**
     * action delete
     */
    public function deleteAction()
    {
        $this->view->assign('json', false);
        $this->view->setVariablesToRender(array(
            'json'
        ));
    }


    /**
     * Validate the user-data and returns the user-uid in case it was successfully
     *
     * @param array $userData
     * @return integer
     */
    private function validateUser($userData): int
    {
        $userUid = intval($userData['uid']);
        if ($userUid < 1) {
            $response = new Response();
            $response->setError(403, "User/Token not valid");
            $response->setSuccess(false);

            $this->view->assign("json", $response->getJson());
            $this->view->setVariablesToRender([
                "json"
            ]);
            return -1;
        }

        // Check the User-Data
        $tokenExpiration = intval($this->settings['tokenExpiration']);
        $tokenIssued = intval($userData['tx_newt_token_issued']);
        if ($tokenExpiration > 0 && $tokenIssued > 0) {
            $now = new \DateTime();
            $tokenExpireDate = new \DateTime('@' . $tokenIssued);
            $tokenExpireDate->add(new \DateInterval("PT{$tokenExpiration}S"));
            if ($tokenExpireDate->getTimestamp() < $now->getTimestamp()) {
                $response = new Response();
                $response->setError(403, "Token expired");
                $response->setSuccess(false);

                $this->view->assign("json", $response->getJson());
                $this->view->setVariablesToRender([
                    "json"
                ]);
                return -1;
            }
        }

        return $userUid;
    }


    /**
     * Save the image into filadmin and returns the file-ref
     *
     * @param string $imageName
     * @param string $imageBase64
     * @param string $subfolder
     * @return \Infonique\Newt\Domain\Model\FileReference|null
     */
    public function setImageFromBase64($imageName, $imageBase64, $subfolder = null)
    {
        if ($imageBase64 && strlen($imageBase64) < 10) {
            return null;
        }
        /** @var ObjectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var StorageRepository */
        $storageRepository = $objectManager->get(StorageRepository::class);

        $storage = $storageRepository->getDefaultStorage();
        $folder = "newt" . ($subfolder ? ('/' . $subfolder) : '');
        $targetFolder = null;
        if ($storage->hasFolder($folder)) {
            $targetFolder = $storage->getFolder($folder);
        } else {
            $targetFolder = $storage->createFolder($folder);
        }
        $tempFilePath = tempnam(sys_get_temp_dir(), 'media');
        // Write the file
        $content = base64_decode($imageBase64);
        $file = fopen($tempFilePath, "wb");
        fwrite($file, $content);
        fclose($file);

        if (file_exists($tempFilePath)) {
            $movedNewFile = $storage->addFile($tempFilePath, $targetFolder, $imageName);
            /** @var \Infonique\Newt\Domain\Model\FileReference */
            $newFileReference = $objectManager->get(\Infonique\Newt\Domain\Model\FileReference::class);
            $newFileReference->setFile($movedNewFile);
            if ($newFileReference) {
                return $newFileReference;
            }
        }
        return null;
    }
}
