<?php

declare(strict_types=1);

namespace Infonique\Newt\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class BackendUserRepository
{
    public function findUserUidByRequest(\TYPO3\CMS\Extbase\Mvc\Request $request): int
    {
        $user = $request->getHeader("user")[0];
        $token = $request->getHeader("token")[0];

        /** @var ConnectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $userUid = $connectionPool->getConnectionForTable('be_users')->select(
            ['uid'],
            'be_users',
            [
                'username' => $user,
                'tx_newt_token' => $token,
            ]
        )->fetchOne();

        return intval($userUid);
    }

    /**
     * Creates a new token for the current BE-User
     *
     * @return string
     */
    public function updateBackendUserToken($userUid): string
    {
        $userToken = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
        /** @var ConnectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connectionPool->getConnectionForTable('be_users')
            ->update('be_users', ['tx_newt_token' => $userToken], ['uid' => $userUid]);
        return $userToken;
    }
}
