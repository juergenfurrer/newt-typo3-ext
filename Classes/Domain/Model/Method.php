<?php

declare(strict_types=1);

namespace Infonique\Newt\Domain\Model;


/**
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Jürgen Furrer <juergen@infonique.ch>
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
     * users
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Infonique\Newt\Domain\Model\BackendUser>
     */
    protected $users = null;

    /**
     * usergroups
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Infonique\Newt\Domain\Model\BackendUserGroup>
     */
    protected $usergroups = null;

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
        $this->users = $this->users ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
     * Adds a BackendUser
     *
     * @param \Infonique\Newt\Domain\Model\BackendUser $user
     * @return void
     */
    public function addUser(\Infonique\Newt\Domain\Model\BackendUser $user)
    {
        $this->users->attach($user);
    }

    /**
     * Removes a BackendUser
     *
     * @param \Infonique\Newt\Domain\Model\BackendUser $userToRemove The BackendUser to be removed
     * @return void
     */
    public function removeUser(\Infonique\Newt\Domain\Model\BackendUser $userToRemove)
    {
        $this->users->detach($userToRemove);
    }

    /**
     * Returns the users
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Infonique\Newt\Domain\Model\BackendUser> $users
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Sets the users
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Infonique\Newt\Domain\Model\BackendUser> $users
     * @return void
     */
    public function setUsers(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $users)
    {
        $this->users = $users;
    }

    /**
     * Get the value of usergroups
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Infonique\Newt\Domain\Model\BackendUserGroup> $users
     */
    public function getUsergroups()
    {
        return $this->usergroups;
    }

    /**
     * Set the value of usergroups
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Infonique\Newt\Domain\Model\BackendUserGroup> $usergroups
     * @return void
     */
    public function setUsergroups(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $usergroups): self
    {
        $this->usergroups = $usergroups;
        return $this;
    }

    /**
     * Check if the user is allowed for this method
     *
     * @return boolean
     */
    public function isUserAllowed(int $userUid, $userGroups = [])
    {
        foreach ($this->users as $user) {
            if ($user->getUid() == $userUid) {
                return true;
            }
        }
        foreach ($userGroups as $userGroup) {
            foreach ($this->usergroups as $methodUsergroup) {
                if ($methodUsergroup->getUid() == $userGroup) {
                    return true;
                }
            }
        }
        return false;
    }
}
