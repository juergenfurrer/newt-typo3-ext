<?php

declare(strict_types=1);

namespace Swisscode\Newt\Domain\Repository;


/**
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Jürgen Furrer <info@swisscode.sk>
 */

/**
 * The repository for FrontendUsers
 */
class FrontendUserRepository extends BaseRepository
{

    /**
     * @var array
     */
    protected $defaultOrderings = ['crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING];

    /**
     * Finds all users matching the given UserGroup ID
     *
     * @param string $usergroup_id
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface<\Swisscode\Newt\Domain\Model\FrontendUser>
     */
    public function findByUsergroupId($usergroup_id)
    {
        $query = $this->createQuery();
        $query = $query->matching(
            $query->contains('usergroup', $usergroup_id),
        );

        return $query->execute();
    }
}
