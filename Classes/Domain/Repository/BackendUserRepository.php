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
    /**
     * Find user-data from the request (header)
     *
     * @param \TYPO3\CMS\Extbase\Mvc\Request $request
     * @return array
     */
    public function findUserDataByRequest(\TYPO3\CMS\Extbase\Mvc\Request $request): array
    {
        $user = $request->getHeader("user")[0];
        $token = $request->getHeader("token")[0];

        /** @var ConnectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $userData = $connectionPool->getConnectionForTable('be_users')->select(
            ['uid', 'usergroup', 'tx_newt_token', 'tx_newt_token_issued'],
            'be_users',
            [
                'username' => $user,
                'tx_newt_token' => $token,
            ]
        )->fetch();
        if (is_countable($userData)) {
            return $userData;
        }

        return [];
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
            ->update('be_users', [
                'tx_newt_token' => $userToken,
                'tx_newt_token_issued' => time()
            ], ['uid' => $userUid]);
        return $userToken;
    }
}
