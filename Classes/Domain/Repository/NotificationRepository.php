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
 * The repository for Notifications
 */
class NotificationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = ['sendDatetime' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING];

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
     * Find all pending messages
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|object[]
     */
    public function findPendingNotifications()
    {
        $query = $this->createQuery();

        $date = new \DateTime();
        $query->matching(
            $query->logicalAnd(
                $query->logicalOr(
                    $query->lessThanOrEqual('send_datetime', $date->getTimestamp()),
                    $query->lessThanOrEqual('send_datetime', 0),
                ),
                $query->equals('result_datetime', 0)
            )
        );

        return $query->execute();
    }
}
