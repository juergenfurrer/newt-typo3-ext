<?php

declare(strict_types=1);

namespace Infonique\Newt\Domain\Repository;


/**
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Jürgen Furrer <juergen@infonique.ch>
 */

/**
 * The repository for BackendUser
 */
class BackendUserRepository extends \TYPO3\CMS\Extbase\Domain\Repository\BackendUserRepository
{

    /**
     * @var array
     */
    protected $defaultOrderings = ['crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING];

    /**
     * Sets this Repo to ignore the StoragePage and SysLanguage
     */
    public function setDetachedQuerySettings()
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(FALSE);
        $querySettings->setRespectSysLanguage(FALSE);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Finds all users matching the given UserGroup ID
     *
     * @param string $usergroup_id
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface<\Infonique\Newt\Domain\Model\BackendUser>
     */
    public function findByUsergroupId($usergroup_id)
    {
        $query = $this->createQuery();
        $query = $query->matching(
            $query->contains('backendUserGroups', $usergroup_id),
        );

        return $query->execute();
    }
}
