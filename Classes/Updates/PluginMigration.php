<?php

declare(strict_types=1);

namespace Nitsan\NsNewsComments\Updates;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('txNsNewsCommentsPluginMigration')]
class PluginMigration implements UpgradeWizardInterface
{
    /**
     * Map of old list_type values to new CType values.
     * All other configuration (including pi_flexform) stays unchanged.
     */
    private const PLUGIN_MAPPINGS = [
        'nsnewscomments_newscomment' => 'nsnewscomments_newscomment',
    ];

    public function getTitle(): string
    {
        return 'EXT:ns_news_comments: Migrate legacy plugin to CType';
    }

    public function getDescription(): string
    {
        $count = $this->getTotalMigrationCount();
        return 'Migrates legacy News Comments plugin records from CType=list + list_type=nsnewscomments_newscomment to CType=nsnewscomments_newscomment. '
            . 'FlexForm and all other fields stay unchanged. '
            . 'Records to migrate: ' . $count;
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    public function updateNecessary(): bool
    {
        return $this->checkIfWizardIsRequired();
    }

    public function executeUpdate(): bool
    {
        return $this->performMigration();
    }

    public function checkIfWizardIsRequired(): bool
    {
        return $this->getTotalMigrationCount() > 0;
    }

    public function performMigration(): bool
    {
        foreach (self::PLUGIN_MAPPINGS as $legacyListType => $newCType) {
            $records = $this->getMigrationRecordsForListType($legacyListType);

            foreach ($records as $record) {
                $this->updateContentElement((int)$record['uid'], $newCType);
            }
        }

        return true;
    }

    protected function getMigrationRecordsForListType(string $listType): array
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        if ($this->hasListTypeColumn()) {
            return $queryBuilder
                ->select('uid', 'pid', 'CType', 'list_type')
                ->from('tt_content')
                ->where(
                    $queryBuilder->expr()->eq(
                        'CType',
                        $queryBuilder->createNamedParameter('list')
                    ),
                    $queryBuilder->expr()->eq(
                        'list_type',
                        $queryBuilder->createNamedParameter($listType)
                    )
                )
                ->executeQuery()
                ->fetchAllAssociative();
        }

        return [];
    }

    protected function getTotalMigrationCount(): int
    {
        $count = 0;
        foreach (array_keys(self::PLUGIN_MAPPINGS) as $legacyListType) {
            $count += count($this->getMigrationRecordsForListType($legacyListType));
        }

        return $count;
    }

    protected function hasListTypeColumn(): bool
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tt_content');
        $columns = $connection->createSchemaManager()->listTableColumns('tt_content');
        return isset($columns['list_type']);
    }

    /**
     * Updates CType and clears list_type of the given content element UID.
     * Does not touch pi_flexform or any other fields.
     */
    protected function updateContentElement(int $uid, string $newCType): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');

        $queryBuilder->update('tt_content')
            ->set('CType', $newCType);

        if ($this->hasListTypeColumn()) {
            $queryBuilder->set('list_type', '');
        }

        $queryBuilder->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT)
                )
            )
            ->executeStatement();
    }
}