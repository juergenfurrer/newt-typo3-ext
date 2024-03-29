<?php

declare(strict_types=1);

namespace Swisscode\Newt\Controller;

use Swisscode\Newt\Domain\Model\Endpoint;
use Swisscode\Newt\Domain\Model\UserData;
use Swisscode\Newt\NewtApi\Field;
use Swisscode\Newt\NewtApi\FieldType;
use Swisscode\Newt\NewtApi\MethodCreateModel;
use Swisscode\Newt\NewtApi\MethodDeleteModel;
use Swisscode\Newt\NewtApi\MethodListModel;
use Swisscode\Newt\NewtApi\MethodReadModel;
use Swisscode\Newt\NewtApi\MethodType;
use Swisscode\Newt\NewtApi\MethodUpdateModel;
use Swisscode\Newt\NewtApi\ResponseBase;
use Swisscode\Newt\NewtApi\ResponseCreate;
use Swisscode\Newt\NewtApi\ResponseDelete;
use Swisscode\Newt\NewtApi\ResponseList;
use Swisscode\Newt\NewtApi\ResponseRead;
use Swisscode\Newt\NewtApi\ResponseUpdate;
use Swisscode\Newt\Utility\Utils;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Jürgen Furrer <info@swisscode.sk>
 */

/**
 * ApiController
 */
class ApiController extends BaseController
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
     * @var \Swisscode\Newt\Domain\Repository\EndpointRepository
     */
    protected $endpointRepository = null;

    /**
     * @param \Swisscode\Newt\Domain\Repository\EndpointRepository $endpointRepository
     */
    public function injectEndpointRepository(\Swisscode\Newt\Domain\Repository\EndpointRepository $endpointRepository)
    {
        $this->endpointRepository = $endpointRepository;
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
     * Extension-Config
     *
     * @var array
     */
    private $extConf = [];

    /**
     * Logger
     *
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    private $logger;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        $this->extConf = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['newt'];
    }

    /**
     * Returns true, if debugLog is set
     *
     * @return boolean
     */
    private function isDebugLog(): bool
    {
        if (is_array($this->extConf) && isset($this->extConf['debugLog'])) {
            return intval($this->extConf['debugLog']) > 0;
        }
        return false;
    }

    /**
     * Write log
     *
     * @param int $logLevel
     * @param string $message
     * @param array $data
     * @return void
     */
    private function writeLog($logLevel, $message, array $data = [])
    {
        if ($this->logger && $this->isDebugLog()) {
            $this->logger->log($logLevel, $message, $data);
        }
    }

    /**
     * Returns the endpoints of this server
     */
    public function endpointsAction()
    {
        /** @var UserData */
        $userData = $this->userRepository->findUserDataByRequest($this->request, $this->settings['feuserNamePrefix']);
        $userUid = $this->validateUserData($userData);
        if ($userUid > 0) {
            $endpoints = $this->endpointRepository->findAll();
            $json = [];
            /** @var Endpoint $endpoint */
            foreach ($endpoints as $endpoint) {
                $data = $endpoint->getData($userData, $this->settings);
                if ($data) {
                    $json[] = $data;
                }
            }
            $this->view->assign('endpoints', $json);
            $this->view->setVariablesToRender([
                'endpoints'
            ]);

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

            $this->writeLog(LogLevel::DEBUG, json_encode($json), [
                "action" => $this->actionMethodName
            ]);
        }

        return $this->jsonResponse();
    }

    /**
     * action create
     */
    public function createAction()
    {
        $response = new ResponseCreate();

        /** @var UserData */
        $userData = $this->userRepository->findUserDataByRequest($this->request, $this->settings['feuserNamePrefix']);
        $userUid = $this->validateUserData($userData);
        if ($userUid < 1) {
            $this->writeLog(LogLevel::ERROR, "User not found", [
                "action" => $this->actionMethodName
            ]);
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
                if (!$currentMethod || !$currentMethod->isUserAllowed($userData)) {
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
                        /** @var \Swisscode\Newt\NewtApi\EndpointInterface|\Swisscode\Newt\NewtApi\EndpointInterface */
                        $endpointImplementation = GeneralUtility::makeInstance($className);
                        if ($endpointImplementation instanceof \Swisscode\Newt\NewtApi\EndpointOptionsInterface) {
                            foreach ($endpoint->getOptions() as $option) {
                                $endpointImplementation->addEndpointOption($option->getOptionName(), $option->getOptionValue());
                            }
                        }
                        $prams = [];
                        $isValid = true;
                        $hasFileError = false;
                        /** @var Field $field */
                        foreach ($endpointImplementation->getAvailableFields() as $field) {
                            $fieldName = $field->getName();
                            if (isset($_POST[$fieldName])) {
                                if ($field->getType() == FieldType::CHECKBOX) {
                                    $prams[$fieldName] = Utils::isTrue($_POST[$fieldName]);
                                } else if ($field->getType() == FieldType::DATETIME) {
                                    if (!empty($_POST[$fieldName])) {
                                        $prams[$fieldName] = new \DateTime($_POST[$fieldName]);
                                    }
                                } else if ($field->getType() == FieldType::DATE) {
                                    if (!empty($_POST[$fieldName])) {
                                        $prams[$fieldName] = new \DateTime($_POST[$fieldName]);
                                    }
                                } else if ($field->getType() == FieldType::TIME) {
                                    if (!empty($_POST[$fieldName])) {
                                        $prams[$fieldName] = new \DateTime("1900-01-01 " . $_POST[$fieldName]);
                                    }
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
                                        if (! $prams[$fieldName]) {
                                            $hasFileError = true;
                                        }
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
                        if ($hasFileError) {
                            $response = new ResponseBase();
                            $response->setError(400, "File not saved");
                        } else if (!$isValid) {
                            $response = new ResponseBase();
                            $response->setError(400, "Form not valid");
                        } else {
                            $methodCreateModel = new MethodCreateModel();
                            $methodCreateModel->setBackendUserUid($userUid);
                            $methodCreateModel->setParams($prams);
                            $methodCreateModel->setPageUid($endpoint->getPageUid());
                            $item = $endpointImplementation->methodCreate($methodCreateModel);
                            $response->setItem($item);
                            $response->setSuccess(!empty($item->getId()));
                        }
                    }
                }
            }

            $this->view->assign("response", $response);
            $this->view->setVariablesToRender([
                "response"
            ]);

            $this->view->setConfiguration([
                'response' => [
                    '_descend' => [
                        'error' => [
                            '_only' => ['code', 'message']
                        ],
                        'item' => [
                            '_descend' => [
                                'values' => [
                                    '_descendAll' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->writeLog(LogLevel::DEBUG, json_encode($response), [
                "action" => $this->actionMethodName
            ]);
        }

        return $this->jsonResponse();
    }

    /**
     * action read
     */
    public function readAction()
    {
        $response = new ResponseRead();

        /** @var UserData */
        $userData = $this->userRepository->findUserDataByRequest($this->request, $this->settings['feuserNamePrefix']);
        $userUid = $this->validateUserData($userData);
        if ($userUid < 1) {
            $this->writeLog(LogLevel::ERROR, "User not found", [], [
                "action" => $this->actionMethodName
            ]);
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
                $currentMethod = $endpoint->getMethodByType(MethodType::READ);
                if (!$currentMethod || !$currentMethod->isUserAllowed($userData)) {
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
                        $readId = Utils::getRequestHeader("readId", $this->request);
                        if (!empty($readId)) {
                            /** @var \Swisscode\Newt\NewtApi\EndpointInterface|\Swisscode\Newt\NewtApi\EndpointInterface */
                            $endpointImplementation = GeneralUtility::makeInstance($className);
                            if ($endpointImplementation instanceof \Swisscode\Newt\NewtApi\EndpointOptionsInterface) {
                                foreach ($endpoint->getOptions() as $option) {
                                    $endpointImplementation->addEndpointOption($option->getOptionName(), $option->getOptionValue());
                                }
                            }
                            $methodReadModel = new MethodReadModel();
                            $methodReadModel->setReadId($readId);
                            $item = $endpointImplementation->methodRead($methodReadModel);
                            $response->setItem($item);
                            $response->setSuccess($item->getId() == $readId);
                        } else {
                            $response = new ResponseBase();
                            $response->setError(400, "ID missing in request");
                        }
                    }
                }
            }

            $this->view->assign("response", $response);
            $this->view->setVariablesToRender([
                "response"
            ]);

            $this->view->setConfiguration([
                'response' => [
                    '_descend' => [
                        'error' => [
                            '_only' => ['code', 'message']
                        ],
                        'item' => [
                            '_descend' => [
                                'values' => [
                                    '_descendAll' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->writeLog(LogLevel::DEBUG, json_encode($response), [
                "action" => $this->actionMethodName
            ]);
        }

        return $this->jsonResponse();
    }

    /**
     * action update
     */
    public function updateAction()
    {
        $response = new ResponseUpdate();

        /** @var UserData */
        $userData = $this->userRepository->findUserDataByRequest($this->request, $this->settings['feuserNamePrefix']);
        $userUid = $this->validateUserData($userData);
        if ($userUid < 1) {
            $this->writeLog("User not found", LogLevel::ERROR, [
                "action" => $this->actionMethodName
            ]);
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
                $currentMethod = $endpoint->getMethodByType(MethodType::UPDATE);
                if (!$currentMethod || !$currentMethod->isUserAllowed($userData)) {
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
                        $updateId = Utils::getRequestHeader("updateId", $this->request);
                        if (!empty($updateId)) {
                            /** @var \Swisscode\Newt\NewtApi\EndpointInterface|\Swisscode\Newt\NewtApi\EndpointInterface */
                            $endpointImplementation = GeneralUtility::makeInstance($className);
                            if ($endpointImplementation instanceof \Swisscode\Newt\NewtApi\EndpointOptionsInterface) {
                                foreach ($endpoint->getOptions() as $option) {
                                    $endpointImplementation->addEndpointOption($option->getOptionName(), $option->getOptionValue());
                                }
                            }
                            $prams = [];
                            $isValid = true;
                            $hasFileError = false;
                            /** @var Field $field */
                            foreach ($endpointImplementation->getAvailableFields() as $field) {
                                $fieldName = $field->getName();
                                if (isset($_POST[$fieldName])) {
                                    if ($field->getType() == FieldType::CHECKBOX) {
                                        if (!empty($_POST[$fieldName])) {
                                            $prams[$fieldName] = Utils::isTrue($_POST[$fieldName]);
                                        }
                                    } else if ($field->getType() == FieldType::DATETIME) {
                                        if (!empty($_POST[$fieldName])) {
                                            $prams[$fieldName] = new \DateTime($_POST[$fieldName]);
                                        }
                                    } else if ($field->getType() == FieldType::DATE) {
                                        if (!empty($_POST[$fieldName])) {
                                            $prams[$fieldName] = new \DateTime($_POST[$fieldName]);
                                        }
                                    } else if ($field->getType() == FieldType::TIME) {
                                        if (!empty($_POST[$fieldName])) {
                                            $prams[$fieldName] = new \DateTime("1900-01-01 " . $_POST[$fieldName]);
                                        }
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
                                            if (! $prams[$fieldName]) {
                                                $hasFileError = true;
                                            }
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
                            if ($hasFileError) {
                                $response = new ResponseBase();
                                $response->setError(400, "File not saved");
                            } else if (!$isValid) {
                                $response = new ResponseBase();
                                $response->setError(400, "Form not valid");
                            } else {
                                $methodUpdateModel = new MethodUpdateModel();
                                $methodUpdateModel->setBackendUserUid($userUid);
                                $methodUpdateModel->setUpdateId($updateId);
                                $methodUpdateModel->setParams($prams);
                                $item = $endpointImplementation->methodUpdate($methodUpdateModel);
                                $response->setItem($item);
                                $response->setSuccess($item->getId() == $updateId);
                            }
                        } else {
                            $response = new ResponseBase();
                            $response->setError(400, "ID missing in request");
                        }
                    }
                }
            }

            $this->view->assign("response", $response);
            $this->view->setVariablesToRender([
                "response"
            ]);

            $this->view->setConfiguration([
                'response' => [
                    '_descend' => [
                        'error' => [
                            '_only' => ['code', 'message']
                        ],
                        'item' => [
                            '_descend' => [
                                'values' => [
                                    '_descendAll' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->writeLog(LogLevel::DEBUG, json_encode($response), [
                "action" => $this->actionMethodName
            ]);
        }

        return $this->jsonResponse();
    }

    /**
     * action delete
     */
    public function deleteAction()
    {
        $response = new ResponseDelete();

        /** @var UserData */
        $userData = $this->userRepository->findUserDataByRequest($this->request, $this->settings['feuserNamePrefix']);
        $userUid = $this->validateUserData($userData);
        if ($userUid < 1) {
            $this->writeLog(LogLevel::ERROR, "User not found", [
                "action" => $this->actionMethodName
            ]);
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
                $currentMethod = $endpoint->getMethodByType(MethodType::DELETE);
                if (!$currentMethod || !$currentMethod->isUserAllowed($userData)) {
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
                        $deleteId = Utils::getRequestHeader("deleteId", $this->request);
                        if (!empty($deleteId)) {
                            /** @var \Swisscode\Newt\NewtApi\EndpointInterface|\Swisscode\Newt\NewtApi\EndpointInterface */
                            $endpointImplementation = GeneralUtility::makeInstance($className);
                            if ($endpointImplementation instanceof \Swisscode\Newt\NewtApi\EndpointOptionsInterface) {
                                foreach ($endpoint->getOptions() as $option) {
                                    $endpointImplementation->addEndpointOption($option->getOptionName(), $option->getOptionValue());
                                }
                            }
                            $methodDeleteModel = new MethodDeleteModel();
                            $methodDeleteModel->setDeleteId($deleteId);
                            $res = $endpointImplementation->methodDelete($methodDeleteModel);
                            if (! $res) {
                                $response->setError(400, "Item could not be deleted");
                            }
                            $response->setSuccess($res);
                        } else {
                            $response->setError(400, "ID missing in request");
                        }
                    }
                }
            }
        }

        $this->view->assign("response", $response);
        $this->view->setVariablesToRender([
            "response"
        ]);

        $this->writeLog(LogLevel::DEBUG, json_encode($response), [
            "action" => $this->actionMethodName
        ]);

        return $this->jsonResponse();
    }

    /**
     * action list
     */
    public function listAction()
    {
        $response = new ResponseList();
        $result = [];

        /** @var UserData */
        $userData = $this->userRepository->findUserDataByRequest($this->request, $this->settings['feuserNamePrefix']);
        $userUid = $this->validateUserData($userData);
        if ($userUid < 1) {
            $this->writeLog(LogLevel::ERROR, "User not found", [
                "action" => $this->actionMethodName
            ]);
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
                if (!$currentMethod || !$currentMethod->isUserAllowed($userData)) {
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
                        /** @var \Swisscode\Newt\NewtApi\EndpointInterface|\Swisscode\Newt\NewtApi\EndpointInterface */
                        $endpointImplementation = GeneralUtility::makeInstance($className);
                        if ($endpointImplementation instanceof \Swisscode\Newt\NewtApi\EndpointOptionsInterface) {
                            foreach ($endpoint->getOptions() as $option) {
                                $endpointImplementation->addEndpointOption($option->getOptionName(), $option->getOptionValue());
                            }
                        }
                        $methodListModel = new MethodListModel();
                        $methodListModel->setBackendUserUid($userUid);
                        $methodListModel->setPageUid($endpoint->getPageUid());

                        $pageSize = Utils::getRequestHeader("pageSize", $this->request);
                        if ($pageSize) {
                            if (intval($pageSize) > 0) {
                                $methodListModel->setPageSize(intval($pageSize));
                            }
                        }

                        $lastKnownItemId = Utils::getRequestHeader("lastKnownItemId", $this->request);
                        if ($lastKnownItemId) {
                            if (!empty($lastKnownItemId)) {
                                $methodListModel->setLastKnownItemId($lastKnownItemId);
                            }
                        }

                        $result = $endpointImplementation->methodList($methodListModel);
                        $response->setSuccess(true);
                    }
                }
            }
            $response->setItems($result);
        }

        $this->view->assign("response", $response);
        $this->view->setVariablesToRender([
            "response"
        ]);

        $this->view->setConfiguration([
            'response' => [
                '_descend' => [
                    'error' => [
                        '_only' => ['code', 'message']
                    ],
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

        $this->writeLog(LogLevel::DEBUG, json_encode($response), [
            "action" => $this->actionMethodName
        ]);

        return $this->jsonResponse();
    }


    /**
     * Validate the user-data and returns the user-uid in case it was successfully
     *
     * @param UserData|null $userData
     * @return integer
     */
    private function validateUserData(?UserData $userData): int
    {
        $response = new ResponseBase();

        $userUid = $userData ? intval($userData->getUid()) : 0;
        if ($userUid < 1) {
            $response->setError(403, "User/Token not valid");

            $this->view->assign("response", $response);
            $this->view->setVariablesToRender([
                "response"
            ]);
            $this->writeLog(LogLevel::ERROR, $response->getError(), [
                "action" => $this->actionMethodName
            ]);
            return -1;
        }

        // Check the User-Data
        $tokenExpiration = intval($this->settings['tokenExpiration']);
        $tokenIssued = $userData->getTokenIssued();
        if ($tokenExpiration > 0 && $tokenIssued) {
            $now = new \DateTime();
            $tokenExpireDate = new \DateTime('@' . $tokenIssued->getTimestamp());
            $tokenExpireDate->add(new \DateInterval("PT{$tokenExpiration}S"));
            if ($tokenExpireDate->getTimestamp() < $now->getTimestamp()) {
                $response->setError(403, "Token expired");
                $this->view->assign("response", $response);
                $this->view->setVariablesToRender([
                    "response"
                ]);
                $this->writeLog(LogLevel::ERROR, $response->getError(), [
                    "action" => $this->actionMethodName
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
     * @return \Swisscode\Newt\Domain\Model\FileReference|null
     */
    private function setFileFromBase64($imageName, $imageBase64, $subfolder = null)
    {
        if ($imageBase64 && strlen($imageBase64) < 10) {
            return null;
        }
        /** @var StorageRepository */
        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);

        $fileStorageId = intval($this->settings['fileStorageId']);
        if ($fileStorageId > 0) {
            $storage = $storageRepository->findByUid($fileStorageId);
        } else if (method_exists($storageRepository, 'getDefaultStorage')) {
            $storage = $storageRepository->getDefaultStorage();
        } else {
            $storage = $storageRepository->findByUid(1);
        }

        if (! $storage) {
            return null;
        }

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
            /** @var \Swisscode\Newt\Domain\Model\FileReference */
            $newFileReference = GeneralUtility::makeInstance(\Swisscode\Newt\Domain\Model\FileReference::class);
            $newFileReference->setFile($movedNewFile);
            if ($newFileReference) {
                return $newFileReference;
            }
        }
        return null;
    }
}
