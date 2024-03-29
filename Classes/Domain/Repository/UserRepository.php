<?php

declare(strict_types=1);

namespace Swisscode\Newt\Domain\Repository;

use Swisscode\Newt\Domain\Model\UserData;
use Swisscode\Newt\Utility\Utils;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Jürgen Furrer <info@swisscode.sk>
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
     * @param string|null $feuserNamePrefix
     * @return UserData|null
     */
    public function findUserDataByRequest(\TYPO3\CMS\Extbase\Mvc\Request $request, ?string $feuserNamePrefix = null): ?UserData
    {
        $user = Utils::getRequestHeader("user", $request);
        $token = Utils::getRequestHeader("token", $request);

        $tryBE = false;
        $tryFE = false;
        if (empty($feuserNamePrefix)) {
            // try BE, then FE
            $tryBE = true;
            $tryFE = true;
        } else {
            $feuserNamePrefixLen = strlen($feuserNamePrefix);
            if (!empty($user) && substr($user, 0, $feuserNamePrefixLen) == $feuserNamePrefix) {
                // FE user only
                $user = substr($user, $feuserNamePrefixLen);
                $tryFE = true;
            } else {
                // BE user only
                $tryBE = true;
            }
        }

        /** @var ConnectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        if ($tryBE) {
            $userData = $connectionPool->getConnectionForTable('be_users')->select(
                ['uid', 'usergroup', 'admin', 'tx_newt_token', 'tx_newt_token_issued'],
                'be_users',
                [
                    'username' => $user,
                    'tx_newt_token' => $token,
                ]
            )->fetch();
            if (is_countable($userData)) {
                $ud = new UserData($userData);
                $ud->setType("BE");
                return $ud;
            }
        }

        if ($tryFE) {
            $userData = $connectionPool->getConnectionForTable('fe_users')->select(
                ['uid', 'usergroup', 'tx_newt_token', 'tx_newt_token_issued'],
                'fe_users',
                [
                    'username' => $user,
                    'tx_newt_token' => $token,
                ]
            )->fetch();
            if (is_countable($userData)) {
                $ud = new UserData($userData);
                $ud->setType("FE");
                return $ud;
            }
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
