<?php

declare(strict_types=1);

namespace Infonique\Newt\Domain\Model;

use Infonique\Newt\Utility\Utils;
use Infonique\Newt\NewtApi\EndpointOptions;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
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
 * Endpoint
 */
class Endpoint extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * description
     *
     * @var string
     */
    protected $description = '';

    /**
     * endpointClass
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $endpointClass = '';

    /** @var string */
    protected $option1 = '';

    /** @var string */
    protected $option2 = '';

    /** @var string */
    protected $option3 = '';

    /**
     * pageUid
     *
     * @var int
     */
    protected $pageUid = 0;

    /**
     * methods
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Infonique\Newt\Domain\Model\Method>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $methods = null;


    /**
     * Returns the json-object of this object based on the need of the Newt App
     *
     * @param UserData $userData
     * @param array $settings
     * @return object|null
     */
    public function getData(UserData $userData, $settings = [])
    {
        // Create an instance of the requested class
        $className = $this->getEndpointClass();
        try {
            if (! class_exists($className)) {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }

        $shouldExport = false;

        /** @var \Infonique\Newt\NewtApi\EndpointInterface */
        $endpointImplementation = GeneralUtility::makeInstance($className);
        $endpointImplementation->setEndpointOptions($this->getEndpointOptions());

        /** @var \Infonique\Newt\NewtApi\Endpoint */
        $endpoint = GeneralUtility::makeInstance(\Infonique\Newt\NewtApi\Endpoint::class);
        $endpoint->setUid($this->getUid());
        $endpoint->setName($this->getName());
        $endpoint->setDescription($this->getDescription());
        $endpoint->setMaxAllowedBytes(Utils::getFileUploadMaxSize());

        /** @var \Infonique\Newt\NewtApi\Configuration */
        $configuration = GeneralUtility::makeInstance(\Infonique\Newt\NewtApi\Configuration::class);
        /** @var \Infonique\Newt\Domain\Model\Method $method */
        foreach ($this->getMethods() as $method) {
            if (in_array($method->getType(), $endpointImplementation->getAvailableMethodTypes())) {
                $allowed = $method->isUserAllowed($userData);

                if ($allowed) {
                    $shouldExport = true;
                    /** @var \Infonique\Newt\NewtApi\Method */
                    $newtMethod = GeneralUtility::makeInstance(\Infonique\Newt\NewtApi\Method::class);
                    $newtMethod->setType($method->getType());

                    /** @var ObjectManager */
                    $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
                    /** @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder */
                    $uriBuilder = $objectManager->get(UriBuilder::class);
                    $uri = $uriBuilder->reset()
                        ->setCreateAbsoluteUri(true)
                        ->setTargetPageUid(intval($settings['apiPageId'] ?? 1))
                        ->setTargetPageType(intval($settings['apiTypeNum']))
                        ->setArguments(['tx_newt_api' => ["uid" => $this->getUid()]])
                        ->uriFor($method->getType(), [], "Api", "Newt", "Api");

                    $apiBaseUrl = trim($settings['apiBaseUrl']);
                    $newtMethod->setUrl($apiBaseUrl . $uri);
                    $configuration->addMethod($newtMethod);
                }
            }
        }

        // Set the fields
        $configuration->setFields($endpointImplementation->getAvailableFields());

        // Add the configuration
        $endpoint->setConfiguration($configuration);

        return $shouldExport ? (object)($endpoint->getData()) : null;
    }

    /**
     * __construct
     */
    public function __construct()
    {
        $this->initializeObject();
    }

    /**
     * Initializes all ObjectStorage properties when model is reconstructed from DB (where __construct is not called)
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->methods = $this->methods ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Adds a Method
     *
     * @param \Infonique\Newt\Domain\Model\Method $method
     * @return void
     */
    public function addMethod(\Infonique\Newt\Domain\Model\Method $method)
    {
        $this->methods->attach($method);
    }

    /**
     * Removes a Method
     *
     * @param \Infonique\Newt\Domain\Model\Method $methodToRemove The Method to be removed
     * @return void
     */
    public function removeMethod(\Infonique\Newt\Domain\Model\Method $methodToRemove)
    {
        $this->methods->detach($methodToRemove);
    }

    /**
     * Returns the methods
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Infonique\Newt\Domain\Model\Method> $methods
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Returns the methods
     *
     * @param string $methodType
     * @return \Infonique\Newt\Domain\Model\Method|null $method
     */
    public function getMethodByType($methodType)
    {
        foreach ($this->methods as $method) {
            if ($method->getType() == $methodType) {
                return $method;
            }
        }
        return null;
    }

    /**
     * Sets the methods
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Infonique\Newt\Domain\Model\Method> $methods
     * @return void
     */
    public function setMethods(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $methods)
    {
        $this->methods = $methods;
    }

    /**
     * Get the value of pageUid
     */
    public function getPageUid()
    {
        return $this->pageUid;
    }

    /**
     * Set the value of pageUid
     */
    public function setPageUid($pageUid): self
    {
        $this->pageUid = $pageUid;
        return $this;
    }

    /**
     * Returns the endpointClass
     *
     * @return string $endpointClass
     */
    public function getEndpointClass()
    {
        return $this->endpointClass;
    }

    /**
     * Sets the endpointClass
     *
     * @param string $endpointClass
     * @return void
     */
    public function setEndpointClass(string $endpointClass)
    {
        $this->endpointClass = $endpointClass;
    }

    /**
     * Returns the options as EndpointOptions
     *
     * @return EndpointOptions
     */
    public function getEndpointOptions(): EndpointOptions
    {
        $endpointOptions = new EndpointOptions();
        $endpointOptions->setOption1($this->getOption1() ?? '');
        $endpointOptions->setOption2($this->getOption2() ?? '');
        $endpointOptions->setOption3($this->getOption3() ?? '');
        return $endpointOptions;
    }

    /**
     * Get the value of option1
     */
    public function getOption1()
    {
        return $this->option1;
    }

    /**
     * Set the value of option1
     *
     * @return  self
     */
    public function setOption1($option1)
    {
        $this->option1 = $option1;
        return $this;
    }

    /**
     * Get the value of option2
     */
    public function getOption2()
    {
        return $this->option2;
    }

    /**
     * Set the value of option2
     *
     * @return  self
     */
    public function setOption2($option2)
    {
        $this->option2 = $option2;
        return $this;
    }

    /**
     * Get the value of option3
     */
    public function getOption3()
    {
        return $this->option3;
    }

    /**
     * Set the value of option3
     *
     * @return  self
     */
    public function setOption3($option3)
    {
        $this->option3 = $option3;
        return $this;
    }
}
