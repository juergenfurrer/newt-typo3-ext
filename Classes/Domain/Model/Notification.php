<?php

declare(strict_types=1);

namespace Swisscode\Newt\Domain\Model;


/**
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Jürgen Furrer <info@swisscode.sk>
 */

/**
 * Notification
 */
class Notification extends \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject
{

    /**
     * title
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $title = '';

    /**
     * message
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $message = '';

    /**
     * sendDatetime
     *
     * @var \DateTime
     */
    protected $sendDatetime = null;

    /**
     * resultDatetime
     *
     * @var \DateTime
     */
    protected $resultDatetime = null;

    /**
     * result
     *
     * @var string
     */
    protected $result = '';

    /**
     * isTopic
     *
     * @var boolean
     */
    protected $isTopic = false;

    /**
     * beusers
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Swisscode\Newt\Domain\Model\BackendUser>
     */
    protected $beusers = null;

    /**
     * feusers
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Swisscode\Newt\Domain\Model\FrontendUser>
     */
    protected $feusers = null;

    /**
     * beusergroups
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Swisscode\Newt\Domain\Model\BackendUserGroup>
     */
    protected $beusergroups = null;

    /**
     * feusergroups
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Swisscode\Newt\Domain\Model\FrontendUserGroup>
     */
    protected $feusergroups = null;


    /**
     * __construct
     */
    public function __construct()
    {
        // Do not remove the next line: It would break the functionality
        $this->initializeObject();
    }

    /**
     * Initializes all ObjectStorage properties when model is reconstructed from DB (where __construct is not called)
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->beusers = $this->beusers ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->feusers = $this->feusers ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->beusergroups = $this->beusergroups ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->feusergroups = $this->feusergroups ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Get the value of title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     */
    public function setTitle($title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the value of message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of message
     */
    public function setMessage($message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get the value of sendDatetime
     */
    public function getSendDatetime()
    {
        return $this->sendDatetime;
    }

    /**
     * Set the value of sendDatetime
     */
    public function setSendDatetime($sendDatetime): self
    {
        $this->sendDatetime = $sendDatetime;
        return $this;
    }

    /**
     * Get the value of resultDatetime
     */
    public function getResultDatetime()
    {
        return $this->resultDatetime;
    }

    /**
     * Set the value of resultDatetime
     */
    public function setResultDatetime($resultDatetime): self
    {
        $this->resultDatetime = $resultDatetime;
        return $this;
    }

    /**
     * Get the value of result
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set the value of result
     */
    public function setResult($result): self
    {
        $this->result = $result;
        return $this;
    }

    /**
     * Get the value of isTopic
     */
    public function getIsTopic()
    {
        return $this->isTopic;
    }

    /**
     * Set the value of isTopic
     */
    public function setIsTopic($isTopic): self
    {
        $this->isTopic = $isTopic;
        return $this;
    }

    /**
     * Get the value of beusers
     */
    public function getBeusers()
    {
        return $this->beusers;
    }

    /**
     * Set the value of beusers
     */
    public function setBeusers($beusers): self
    {
        $this->beusers = $beusers;
        return $this;
    }

    /**
     * Get the value of feusers
     */
    public function getFeusers()
    {
        return $this->feusers;
    }

    /**
     * Set the value of feusers
     */
    public function setFeusers($feusers): self
    {
        $this->feusers = $feusers;
        return $this;
    }

    /**
     * Get the value of beusergroups
     */
    public function getBeusergroups()
    {
        return $this->beusergroups;
    }

    /**
     * Set the value of beusergroups
     */
    public function setBeusergroups($beusergroups): self
    {
        $this->beusergroups = $beusergroups;
        return $this;
    }

    /**
     * Get the value of feusergroups
     */
    public function getFeusergroups()
    {
        return $this->feusergroups;
    }

    /**
     * Set the value of feusergroups
     */
    public function setFeusergroups($feusergroups): self
    {
        $this->feusergroups = $feusergroups;
        return $this;
    }
}
