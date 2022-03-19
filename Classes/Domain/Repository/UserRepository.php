<?php

declare(strict_types=1);

namespace Infonique\Newt\Domain\Repository;

use Infonique\Newt\Domain\Model\UserData;
use Infonique\Newt\Utility\Utils;
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
 * The repository for User (fe / be)
 */
class UserRepository
{
    /**
     * Find user-data from the request (header)
     *
     * @param \TYPO3\CMS\Extbase\Mvc\Request $request
     * @return UserData|null
     */
    public function findUserDataByRequest(\TYPO3\CMS\Extbase\Mvc\Request $request): ?UserData
    {
        $user = Utils::getRequestHeader("user", $request);
        $token = Utils::getRequestHeader("token", $request);

        /** @var ConnectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $userData = $connectionPool->getConnectionForTable('be_users')->select(
            ['uid', 'usergroup', 'admin', 'tx_newt_token', 'tx_newt_token_issued'],
            'be_users',
            [
                'username' => $user,
                'tx_newt_token' => $token,
            ]
        )->fetch();
        if (is_countable($userData)) {
            return new UserData($userData);
        }

        $userData = $connectionPool->getConnectionForTable('fe_users')->select(
            ['uid', 'usergroup', 'tx_newt_token', 'tx_newt_token_issued'],
            'fe_users',
            [
                'username' => $user,
                'tx_newt_token' => $token,
            ]
        )->fetch();
        if (is_countable($userData)) {
            return new UserData($userData);
        }

        return null;
    }

    /**
     * Creates a new token for the current BE-User
     *
     * @return string
     */
    public function updateBackendUserToken($userUid): string
    {
        $userToken = Utils::getUuid();
        /** @var ConnectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connectionPool->getConnectionForTable('be_users')
            ->update('be_users', [
                'tx_newt_token' => $userToken,
                'tx_newt_token_issued' => time()
            ], ['uid' => $userUid]);
        return $userToken;
    }

    /**
     * Creates a new token for the current FE-User
     *
     * @return string
     */
    public function updateFrontendUserToken($userUid): string
    {
        $userToken = Utils::getUuid();
        /** @var ConnectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connectionPool->getConnectionForTable('fe_users')
            ->update('fe_users', [
                'tx_newt_token' => $userToken,
                'tx_newt_token_issued' => time()
            ], ['uid' => $userUid]);
        return $userToken;
    }
}
