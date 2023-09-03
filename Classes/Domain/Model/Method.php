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
 * Method
 */
class Method extends \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject
{

    /**
     * type
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $type = '';

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
     * Returns the data for this Object
     *
     * @return void
     */
    public function getData()
    {
        return (object)[
            "type" => $this->getType()
        ];
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
        $this->beusergroups = $this->beusergroups ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->feusergroups = $this->feusergroups ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param string $type
     * @return void
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * Get the value of beusergroups
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Swisscode\Newt\Domain\Model\BackendUserGroup> $users
     */
    public function getBeusergroups()
    {
        return $this->beusergroups;
    }

    /**
     * Set the value of beusergroups
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Swisscode\Newt\Domain\Model\BackendUserGroup> $feusergroups
     * @return self
     */
    public function setBeusergroups($beusergroups): self
    {
        $this->beusergroups = $beusergroups;
        return $this;
    }

    /**
     * Get the value of feusergroups
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Swisscode\Newt\Domain\Model\FrontendUserGroup> $users
     */
    public function getFeusergroups()
    {
        return $this->feusergroups;
    }

    /**
     * Set the value of feusergroups
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Swisscode\Newt\Domain\Model\FrontendUserGroup> $feusergroups
     * @return self
     */
    public function setFeusergroups($feusergroups): self
    {
        $this->feusergroups = $feusergroups;
        return $this;
    }


    /**
     * Check if the user is allowed for this method
     *
     * @param UserData $userData
     * @return boolean
     */
    public function isUserAllowed(UserData $userData)
    {
        if ($userData->getType() == "BE") {
            // BE-User
            if ($userData->getIsAdmin()) {
                // Admin is allowed
                return true;
            }
            foreach ($userData->getUsergroups() as $userGroup) {
                foreach ($this->beusergroups as $methodUsergroup) {
                    if ($methodUsergroup->getUid() == $userGroup) {
                        // User in Group
                        return true;
                    }
                }
            }
            return false;
        } else {
            // FE-User
            foreach ($userData->getUsergroups() as $userGroup) {
                foreach ($this->feusergroups as $methodUsergroup) {
                    if ($methodUsergroup->getUid() == $userGroup) {
                        // User in Group
                        return true;
                    }
                }
            }
            return false;
        }
    }
}
