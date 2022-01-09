<?php

declare(strict_types=1);

namespace Infonique\Newt\Controller;

use Infonique\Newt\Domain\Model\Endpoint;
use Infonique\Newt\NewtApi\Field;
use Infonique\Newt\NewtApi\FieldType;
use Infonique\Newt\NewtApi\MethodCreateModel;
use Infonique\Newt\NewtApi\MethodListModel;
use Infonique\Newt\NewtApi\MethodType;
use Infonique\Newt\NewtApi\ResponseBase;
use Infonique\Newt\NewtApi\ResponseCreate;
use Infonique\Newt\NewtApi\ResponseDelete;
use Infonique\Newt\NewtApi\ResponseList;
use Infonique\Newt\NewtApi\ResponseRead;
use Infonique\Newt\NewtApi\ResponseUpdate;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
        $response = new ResponseCreate();
        $result = 0;

        $userData = $this->backendUserRepository->findUserDataByRequest($this->request);
        $userUid = $this->validateUser($userData);
        if ($userUid < 1) {
            return;
        } else {
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
                if (!$currentMethod || !$currentMethod->isUserAllowed($userUid)) {
                    $response->setError(403, "User not allowed");
                } else {
                    $className = $endpoint->getEndpointClass();
                    $classExists = false;
                    try {
                        if (class_exists($className)) {
                            $classExists = true;
                        }
                    } catch (\Exception $e) {
                        $classExists = false;
                    }

                    if (!$classExists) {
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
                                } else if ($field->getType() == FieldType::IMAGE || $field->getType() == FieldType::FILE) {
                                    if (strlen($_POST[$fieldName]) > 0) {
                                        $token = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
                                        $fileName = md5($_POST[$fieldName] . $token);
                                        $extension = "any";
                                        if ($field->getType() == FieldType::IMAGE) {
                                            $extension = "jpg";
                                        } else {
                                            if (isset($_POST[$fieldName . "FileName"])) {
                                                $pathinfo = pathinfo($_POST[$fieldName . "FileName"]);
                                                $fileName .= "_" . $pathinfo['filename'];
                                                $extension = $pathinfo['extension'];
                                            }
                                        }
                                        $prams[$fieldName] = $this->setFileFromBase64($fileName . "." . $extension, $_POST[$fieldName], "be_user_" . $userUid);
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
                        if (!$isValid) {
                            $response->setError(400, "Form not valid");
                        } else {
                            $methodCreateModel = new MethodCreateModel();
                            $methodCreateModel->setBackendUserUid($userUid);
                            $methodCreateModel->setParams($prams);
                            $methodCreateModel->setPageUid($endpoint->getPageUid());
                            $result = $endpointImplementation->methodCreate($methodCreateModel);
                            $response->setCreatedId($result);
                        }
                    }
                }
            }
            $response->setSuccess(intval($result) > 0);

            $this->view->assign("response", $response);
            $this->view->setVariablesToRender([
                "response"
            ]);
        }
    }

    /**
     * action read
     */
    public function readAction()
    {
        $this->view->assign("response", new ResponseRead());
        $this->view->setVariablesToRender(array(
            "response"
        ));
    }

    /**
     * action update
     */
    public function updateAction()
    {
        $this->view->assign("response", new ResponseUpdate());
        $this->view->setVariablesToRender(array(
            "response"
        ));
    }

    /**
     * action delete
     */
    public function deleteAction()
    {
        $this->view->assign("response", new ResponseDelete());
        $this->view->setVariablesToRender(array(
            "response"
        ));
    }

    /**
     * action list
     */
    public function listAction()
    {
        $response = new ResponseList();
        $result = [];

        $userData = $this->backendUserRepository->findUserDataByRequest($this->request);
        $userUid = $this->validateUser($userData);
        if ($userUid < 1) {
            return;
        } else {
            $endpointUid = 0;
            if ($this->request->hasArgument('uid')) {
                $endpointUid = $this->request->getArgument('uid');
            }
            /** @var Endpoint */
            $endpoint = $this->endpointRepository->findByUid(intval($endpointUid));
            if (!$endpoint) {
                $response->setError(404, "Endpoint not found");
            } else {
                $currentMethod = $endpoint->getMethodByType(MethodType::LIST);
                if (!$currentMethod || !$currentMethod->isUserAllowed($userUid)) {
                    $response->setError(403, "User not allowed");
                } else {
                    $className = $endpoint->getEndpointClass();
                    $classExists = false;
                    try {
                        if (class_exists($className)) {
                            $classExists = true;
                        }
                    } catch (\Exception $e) {
                        $classExists = false;
                    }

                    if (!$classExists) {
                        $response->setError(404, "EndpointClass not found");
                    } else {
                        /** @var \Infonique\Newt\NewtApi\EndpointInterface */
                        $endpointImplementation = new $className();
                        $methodListModel = new MethodListModel();
                        $methodListModel->setBackendUserUid($userUid);
                        $methodListModel->setPageUid($endpoint->getPageUid());
                        $result = $endpointImplementation->methodList($methodListModel);
                        $response->setSuccess(true);
                    }
                }
            }
            $response->setItems($result);
        }

        $this->view->assign("response", $response);
        $this->view->setVariablesToRender(array(
            "response"
        ));

        $this->view->setConfiguration([
            'response' => [
                '_descend' => [
                    'items' => [
                        '_descendAll' => [
                            '_descend' => [
                                'values' => [
                                    '_descendAll' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }


    /**
     * Validate the user-data and returns the user-uid in case it was successfully
     *
     * @param array $userData
     * @return integer
     */
    private function validateUser($userData): int
    {
        $response = new ResponseBase();

        $userUid = intval($userData['uid']);
        if ($userUid < 1) {
            $response->setError(403, "User/Token not valid");

            $this->view->assign("response", $response);
            $this->view->setVariablesToRender([
                "response"
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
                $response->setError(403, "Token expired");
                $this->view->assign("response", $response);
                $this->view->setVariablesToRender([
                    "response"
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
    private function setFileFromBase64($imageName, $imageBase64, $subfolder = null)
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
