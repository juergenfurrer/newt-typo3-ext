<?php

namespace Swisscode\Newt\Utility;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\DataHandling\Model\RecordStateFactory;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;

/**
 * SlugUtility
 */
class SlugUtility
{
    /**
     * Populate empty slugs in custom table
     *
     * Inspired by TYPO3\CMS\Install\Updates\PopulatePageSlugs
     * Workspaces are not respected here!
     *
     * @param string $tableName
     * @param string $fieldName
     * @return void
     */
    public static function populateEmptySlugsInCustomTable($tableName, $fieldName)
    {
        /** @var ConnectionPool */
        $ConnectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        /** @var Connection */
        $connection = $ConnectionPool->getConnectionForTable($tableName);
        /** @var QueryBuilder */
        $queryBuilder = $connection->createQueryBuilder();
        /** @var DeletedRestriction */
        $deletedRestriction = GeneralUtility::makeInstance(DeletedRestriction::class);
        /** @var QueryBuilder */
        $queryBuilder->getRestrictions()->removeAll()->add($deletedRestriction);
        $statement = $queryBuilder
            ->select('*')
            ->from($tableName)
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq($fieldName, $queryBuilder->createNamedParameter('')),
                    $queryBuilder->expr()->isNull($fieldName)
                )
            )
            ->addOrderBy('uid', 'asc')
            ->execute();

        $suggestedSlugs = [];

        $fieldConfig = $GLOBALS['TCA'][$tableName]['columns'][$fieldName]['config'];
        $evalInfo = !empty($fieldConfig['eval']) ? GeneralUtility::trimExplode(',', $fieldConfig['eval'], true) : [];
        $hasToBeUniqueInSite = in_array('uniqueInSite', $evalInfo, true);
        $hasToBeUniqueInPid = in_array('uniqueInPid', $evalInfo, true);
        /** @var SlugHelper */
        $slugHelper = GeneralUtility::makeInstance(SlugHelper::class, $tableName, $fieldName, $fieldConfig);

        while ($record = $statement->fetch()) {
            $recordId = (int)$record['uid'];
            $pid = (int)$record['pid'];
            $languageId = (int)$record['sys_language_uid'];
            $pageIdInDefaultLanguage = $languageId > 0 ? (int)$record['l10n_parent'] : $recordId;
            $slug = $suggestedSlugs[$pageIdInDefaultLanguage][$languageId] ?? '';

            if (empty($slug)) {
                $slug = $slugHelper->generate($record, $pid);
            }

            $state = RecordStateFactory::forName($tableName)
                ->fromArray($record, $pid, $recordId);
            if ($hasToBeUniqueInSite && !$slugHelper->isUniqueInSite($slug, $state)) {
                $slug = $slugHelper->buildSlugForUniqueInSite($slug, $state);
            }
            if ($hasToBeUniqueInPid && !$slugHelper->isUniqueInPid($slug, $state)) {
                $slug = $slugHelper->buildSlugForUniqueInPid($slug, $state);
            }

            $connection->update(
                $tableName,
                [$fieldName => $slug],
                ['uid' => $recordId]
            );
        }
    }

}
