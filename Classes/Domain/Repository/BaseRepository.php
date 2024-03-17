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
 * The BaseRepository
 */
class BaseRepository extends Repository
{
    /**
     * Sets this Repo to ignore the StoragePage and SysLanguage
     */
    public function setDetachedQuerySettings()
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $querySettings->setRespectSysLanguage(false);
        $this->setDefaultQuerySettings($querySettings);
    }
}
