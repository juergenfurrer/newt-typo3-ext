<?php

declare(strict_types=1);

namespace Swisscode\Newt\Domain\Model;

use Swisscode\Newt\Utility\Utils;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Jürgen Furrer <info@swisscode.sk>
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
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
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

    /**
     * pageUid
     *
     * @var int
     */
    protected $pageUid = 0;

    /**
     * methods
     *
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Swisscode\Newt\Domain\Model\Method>
     */
    protected $methods = null;

    /**
     * options
     *
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Swisscode\Newt\Domain\Model\EndpointOption>
     */
    protected $options = null;


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

        /** @var \Swisscode\Newt\NewtApi\EndpointInterface|\Swisscode\Newt\NewtApi\EndpointInterface */
        $endpointImplementation = GeneralUtility::makeInstance($className);
        if ($endpointImplementation instanceof \Swisscode\Newt\NewtApi\EndpointOptionsInterface) {
            foreach ($this->getOptions() as $option) {
                $endpointImplementation->addEndpointOption($option->getOptionName(), $option->getOptionValue());
            }
        }

        /** @var \Swisscode\Newt\NewtApi\Endpoint */
        $endpoint = GeneralUtility::makeInstance(\Swisscode\Newt\NewtApi\Endpoint::class);
        $endpoint->setUid($this->getUid());
        $endpoint->setName($this->getName());
        $endpoint->setDescription($this->getDescription());
        $endpoint->setMaxAllowedBytes(Utils::getFileUploadMaxSize());

        /** @var \Swisscode\Newt\NewtApi\Configuration */
        $configuration = GeneralUtility::makeInstance(\Swisscode\Newt\NewtApi\Configuration::class);
        /** @var \Swisscode\Newt\Domain\Model\Method $method */
        foreach ($this->getMethods() as $method) {
            if (in_array($method->getType(), $endpointImplementation->getAvailableMethodTypes())) {
                $allowed = $method->isUserAllowed($userData);

                if ($allowed) {
                    $shouldExport = true;
                    /** @var \Swisscode\Newt\NewtApi\Method */
                    $newtMethod = GeneralUtility::makeInstance(\Swisscode\Newt\NewtApi\Method::class);
                    $newtMethod->setType($method->getType());

                    /** @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder */
                    $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
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
        $this->options = $this->options ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
     * @param \Swisscode\Newt\Domain\Model\Method $method
     * @return void
     */
    public function addMethod(\Swisscode\Newt\Domain\Model\Method $method)
    {
        $this->methods->attach($method);
    }

    /**
     * Removes a Method
     *
     * @param \Swisscode\Newt\Domain\Model\Method $methodToRemove The Method to be removed
     * @return void
     */
    public function removeMethod(\Swisscode\Newt\Domain\Model\Method $methodToRemove)
    {
        $this->methods->detach($methodToRemove);
    }

    /**
     * Returns the methods
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Swisscode\Newt\Domain\Model\Method> $methods
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Returns the methods
     *
     * @param string $methodType
     * @return \Swisscode\Newt\Domain\Model\Method|null $method
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
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Swisscode\Newt\Domain\Model\Method> $methods
     * @return self
     */
    public function setMethods(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $methods): self
    {
        $this->methods = $methods;
        return $this;
    }

    /**
     * Get the value of options
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Swisscode\Newt\Domain\Model\EndpointOption> $methods
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the value of options
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Swisscode\Newt\Domain\Model\EndpointOption> $methods
     */
    public function setOptions(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $options): self
    {
        $this->options = $options;
        return $this;
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
}
