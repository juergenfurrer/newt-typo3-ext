<?php

declare(strict_types=1);

namespace Swisscode\Newt\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Jürgen Furrer <info@swisscode.sk>
 */

/**
 * The repository for BackendUser
 */
class BackendUserRepository extends BaseRepository
{

    /**
     * @var array
     */
    protected $defaultOrderings = ['crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING];

    /**
     * Finds all users matching the given UserGroup ID
     *
     * @param string $usergroup_id
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface<\Swisscode\Newt\Domain\Model\BackendUser>
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
