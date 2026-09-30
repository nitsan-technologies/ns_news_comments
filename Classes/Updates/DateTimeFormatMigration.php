<?php

declare(strict_types=1);

namespace Nitsan\NsNewsComments\Updates;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('txNsNewsCommentsDateTimeFormatMigration')]
class DateTimeFormatMigration implements UpgradeWizardInterface
{
    private const PLUGIN_SIGNATURE = 'nsnewscomments_newscomment';

    private const LEGACY_FIELDS = [
        'settings.custom',
        'settings.customdate',
        'settings.customtime',
    ];

    /**
     * Format fields mapped to the value written when they contain the removed "global" option.
     */
    private const FORMAT_FIELDS = [
        'settings.dateFormat' => 'F j, Y',
        'settings.timeFormat' => 'g:i a',
    ];

    private const LEGACY_GLOBAL_VALUE = 'global';

    public function __construct(
        private readonly ConnectionPool $connectionPool
    ) {}

    public function getTitle(): string
    {
        return 'EXT:ns_news_comments: Migrate date and time format settings';
    }

    public function getDescription(): string
    {
        return 'Keeps the date and time format selected in existing News Comment plugins. '
            . 'Formats from the removed "custom format" option are copied into the new date and time format fields, '
            . 'and the obsolete FlexForm fields are removed. '
            . 'Records to migrate: ' . count($this->getRecordsToMigrate());
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    public function updateNecessary(): bool
    {
        return $this->getRecordsToMigrate() !== [];
    }

    public function executeUpdate(): bool
    {
        $connection = $this->connectionPool->getConnectionForTable('tt_content');

        foreach ($this->getRecordsToMigrate() as $uid => $flexForm) {
            $flexForm['data']['sDEF']['lDEF'] = $this->migrateFields($flexForm['data']['sDEF']['lDEF']);
            $connection->update(
                'tt_content',
                ['pi_flexform' => $this->convertToXml($flexForm)],
                ['uid' => $uid]
            );
        }

        return true;
    }

    /**
     * @return array<int, array> Parsed FlexForm data indexed by tt_content uid
     */
    private function getRecordsToMigrate(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $rows = $queryBuilder
            ->select('uid', 'pi_flexform')
            ->from('tt_content')
            ->where(
                $this->getPluginConstraint($queryBuilder),
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->like(
                        'pi_flexform',
                        $queryBuilder->createNamedParameter('%settings.custom%')
                    ),
                    $queryBuilder->expr()->like(
                        'pi_flexform',
                        $queryBuilder->createNamedParameter('%>' . self::LEGACY_GLOBAL_VALUE . '<%')
                    )
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $records = [];
        foreach ($rows as $row) {
            $flexForm = GeneralUtility::xml2array((string)$row['pi_flexform']);
            $fields = is_array($flexForm) ? ($flexForm['data']['sDEF']['lDEF'] ?? null) : null;
            if (is_array($fields) && $this->needsMigration($fields)) {
                $records[(int)$row['uid']] = $flexForm;
            }
        }

        return $records;
    }

    /**
     * Matches the plugin as content element and as legacy list_type plugin,
     * so the result does not depend on whether the plugin migration wizard ran first.
     */
    private function getPluginConstraint(QueryBuilder $queryBuilder): string
    {
        $constraints = [
            $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter(self::PLUGIN_SIGNATURE)),
        ];

        if ($this->hasListTypeColumn()) {
            $constraints[] = $queryBuilder->expr()->and(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('list')),
                $queryBuilder->expr()->eq('list_type', $queryBuilder->createNamedParameter(self::PLUGIN_SIGNATURE))
            );
        }

        return (string)$queryBuilder->expr()->or(...$constraints);
    }

    private function hasListTypeColumn(): bool
    {
        $columns = $this->connectionPool
            ->getConnectionForTable('tt_content')
            ->createSchemaManager()
            ->listTableColumns('tt_content');

        return isset($columns['list_type']);
    }

    private function needsMigration(array $fields): bool
    {
        foreach (self::LEGACY_FIELDS as $legacyField) {
            if (array_key_exists($legacyField, $fields)) {
                return true;
            }
        }

        foreach (array_keys(self::FORMAT_FIELDS) as $formatField) {
            if ($this->getValue($fields, $formatField) === self::LEGACY_GLOBAL_VALUE) {
                return true;
            }
        }

        return false;
    }

    /**
     * Reproduces the output of previous releases: the custom formats were only
     * rendered when the checkbox was set and both custom formats were filled,
     * otherwise the selected date and time options were used.
     */
    private function migrateFields(array $fields): array
    {
        $customDate = trim($this->getValue($fields, 'settings.customdate'));
        $customTime = trim($this->getValue($fields, 'settings.customtime'));

        if ($this->getValue($fields, 'settings.custom') === '1' && $customDate !== '' && $customTime !== '') {
            $fields['settings.dateFormat']['vDEF'] = $customDate;
            $fields['settings.timeFormat']['vDEF'] = $customTime;
        }

        $useGlobal = false;
        foreach (self::FORMAT_FIELDS as $formatField => $defaultFormat) {
            if ($this->getValue($fields, $formatField) === self::LEGACY_GLOBAL_VALUE) {
                $fields[$formatField]['vDEF'] = $defaultFormat;
                $useGlobal = true;
            }
        }

        if ($useGlobal) {
            $fields['settings.global']['vDEF'] = '1';
        } elseif (!isset($fields['settings.global'])) {
            $fields['settings.global']['vDEF'] = '0';
        }

        foreach (self::LEGACY_FIELDS as $legacyField) {
            unset($fields[$legacyField]);
        }

        return $fields;
    }

    private function getValue(array $fields, string $field): string
    {
        $value = $fields[$field]['vDEF'] ?? '';

        return is_scalar($value) ? (string)$value : '';
    }

    /**
     * FlexFormTools::flexArray2Xml() is internal and its signature differs
     * between TYPO3 v12 and v14, so the same XML is built with the public API.
     */
    private function convertToXml(array $flexForm): string
    {
        $options = [
            'parentTagMap' => [
                'data' => 'sheet',
                'sheet' => 'language',
                'language' => 'field',
                'el' => 'field',
                'field' => 'value',
                'field:el' => 'el',
                'el:_IS_NUM' => 'section',
                'section' => 'itemType',
            ],
            'disableTypeAttrib' => 2,
        ];

        return '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>' . LF
            . GeneralUtility::array2xml($flexForm, '', 0, 'T3FlexForms', 4, $options);
    }
}
