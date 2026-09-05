<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Blur\BlurElysiumSlider\Defaults;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use Doctrine\DBAL\ParameterType;
use Shopware\Core\Defaults as ShopwareDefaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * Copies 4.9 translation text and global media FKs into per-language content_settings.
 *
 * Legacy columns are dropped only after verification succeeds. A failed verify
 * rolls back the DML and leaves the source columns in place so Shopware can retry.
 *
 * @internal
 */
class Migration1781000000ConsolidateContentSettings extends MigrationStep
{
    /**
     * @var array<string, list<string>>
     */
    private const TEXT_COLUMN_MAP = [
        'title' => ['title'],
        'description' => ['description'],
        'button_label' => ['button', 'label'],
        'url' => ['url'],
    ];

    /**
     * @var array<string, list<string>>
     */
    private const MEDIA_COLUMN_MAP = [
        'slide_cover_id' => ['slideCover', 'desktopId'],
        'slide_cover_mobile_id' => ['slideCover', 'mobileId'],
        'slide_cover_tablet_id' => ['slideCover', 'tabletId'],
        'slide_cover_video_id' => ['slideCover', 'videoId'],
        'presentation_media_id' => ['focusImageId'],
    ];

    public function getCreationTimestamp(): int
    {
        return 1781000000;
    }

    public function update(Connection $connection): void
    {
        $hasText = $this->getAvailableTextColumns($connection) !== [];
        $hasMedia = $this->getAvailableMediaColumns($connection) !== [];

        if (!$hasText && !$hasMedia) {
            return;
        }

        $this->ensureContentSettingsColumn($connection);
        $this->assertContentSettingsAreObjects($connection);

        $connection->transactional(function (Connection $connection): void {
            $this->ensureSystemLanguageTranslations($connection);
            $this->migrateTranslationData($connection);
            $this->migrateMediaIds($connection);
            $this->verify($connection);
        });

        if ($this->getAvailableTextColumns($connection) !== []) {
            $this->dropTranslationColumns($connection);
        }

        if ($this->getAvailableMediaColumns($connection) !== []) {
            $this->dropMainTableMediaColumns($connection);
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    protected function ensureContentSettingsColumn(Connection $connection): void
    {
        if ($this->hasColumn($connection, 'blur_elysium_slides_translation', 'content_settings')) {
            return;
        }

        try {
            $connection->executeStatement('
                ALTER TABLE `blur_elysium_slides_translation`
                ADD COLUMN `content_settings` JSON NULL
            ');
        } catch (\Throwable $e) {
            if ($e instanceof NonUniqueFieldNameException) {
                return;
            }

            if (preg_match(Defaults::MIGRATION_COLUMN_EXISTS, $e->getMessage()) === 1) {
                return;
            }

            throw $e;
        }
    }

    /**
     * Creates a system-language translation for every slide that has media FKs
     * but no system-language row. Media was global in 4.9; without this row the
     * storefront default language would lose covers after the FKs are dropped.
     */
    protected function ensureSystemLanguageTranslations(Connection $connection): void
    {
        $availableColumns = $this->getAvailableMediaColumns($connection);
        if ($availableColumns === []) {
            return;
        }

        $systemLanguageId = Uuid::fromHexToBytes(ShopwareDefaults::LANGUAGE_SYSTEM);
        $mediaWhere = implode(' OR ', array_map(
            static fn (string $column): string => 's.`' . $column . '` IS NOT NULL',
            $availableColumns
        ));
        $now = (new \DateTimeImmutable())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT);

        $connection->executeStatement(
            '
            INSERT INTO blur_elysium_slides_translation (
                blur_elysium_slides_id,
                language_id,
                name,
                created_at
            )
            SELECT
                s.id,
                :lang_id,
                COALESCE(
                    NULLIF((
                        SELECT t.name
                        FROM blur_elysium_slides_translation t
                        WHERE t.blur_elysium_slides_id = s.id
                        ORDER BY t.created_at ASC, t.language_id ASC
                        LIMIT 1
                    ), \'\'),
                    LOWER(HEX(s.id))
                ),
                :created_at
            FROM blur_elysium_slides s
            WHERE (' . $mediaWhere . ')
            AND NOT EXISTS (
                SELECT 1 FROM blur_elysium_slides_translation existing
                WHERE existing.blur_elysium_slides_id = s.id
                AND existing.language_id = :lang_id
            )
            ',
            [
                'lang_id' => $systemLanguageId,
                'created_at' => $now,
            ],
            [
                'lang_id' => ParameterType::BINARY,
                'created_at' => ParameterType::STRING,
            ]
        );
    }

    protected function migrateTranslationData(Connection $connection): void
    {
        foreach ($this->getAvailableTextColumns($connection) as $column) {
            $path = self::TEXT_COLUMN_MAP[$column];
            $expression = $this->jsonSetExpression('content_settings', $path, '`' . $column . '`');

            $connection->executeStatement(
                'UPDATE blur_elysium_slides_translation
                 SET content_settings = ' . $expression . '
                 WHERE `' . $column . '` IS NOT NULL AND `' . $column . '` <> \'\''
            );
        }
    }

    protected function migrateMediaIds(Connection $connection): void
    {
        foreach ($this->getAvailableMediaColumns($connection) as $column) {
            $path = self::MEDIA_COLUMN_MAP[$column];
            $expression = $this->jsonSetExpression(
                't.content_settings',
                $path,
                'LOWER(HEX(s.`' . $column . '`))'
            );

            $connection->executeStatement(
                'UPDATE blur_elysium_slides_translation t
                 INNER JOIN blur_elysium_slides s ON s.id = t.blur_elysium_slides_id
                 SET t.content_settings = ' . $expression . '
                 WHERE s.`' . $column . '` IS NOT NULL'
            );
        }
    }

    protected function verify(Connection $connection): void
    {
        $idSelects = array_merge(
            $this->translationFailureIdSelects($connection),
            $this->mediaFailureIdSelects($connection)
        );

        if ($idSelects === []) {
            return;
        }

        $union = implode(' UNION ', $idSelects);
        $count = (int) $connection->fetchOne('SELECT COUNT(*) FROM (' . $union . ') failed_slides');
        if ($count === 0) {
            return;
        }

        $sampleRows = $connection->fetchFirstColumn(
            'SELECT LOWER(HEX(id)) FROM (' . $union . ') failed_slides LIMIT 5'
        );
        $sample = implode(', ', $sampleRows);

        throw new \RuntimeException(\sprintf(
            'Elysium contentSettings migration verification failed for %d slide(s). Sample ids: %s. Legacy columns were not dropped.',
            $count,
            $sample
        ));
    }

    protected function dropTranslationColumns(Connection $connection): void
    {
        $columns = array_values(array_filter(
            array_keys(self::TEXT_COLUMN_MAP),
            fn (string $column): bool => $this->hasColumn($connection, 'blur_elysium_slides_translation', $column)
        ));

        if ($columns === []) {
            return;
        }

        $clauses = implode(', ', array_map(
            static fn (string $column): string => 'DROP COLUMN `' . $column . '`',
            $columns
        ));

        try {
            $connection->executeStatement(
                'ALTER TABLE `blur_elysium_slides_translation` ' . $clauses
            );
        } catch (\Throwable $e) {
            if (preg_match(Defaults::MIGRATION_COLUMN_NOT_EXISTS, $e->getMessage()) !== 1) {
                throw $e;
            }
        }
    }

    protected function dropMainTableMediaColumns(Connection $connection): void
    {
        $columns = array_keys(self::MEDIA_COLUMN_MAP);

        $fkConstraints = $connection->fetchAllAssociative(
            "SELECT tc.CONSTRAINT_NAME, kcu.COLUMN_NAME
             FROM information_schema.TABLE_CONSTRAINTS tc
             INNER JOIN information_schema.KEY_COLUMN_USAGE kcu
                 ON tc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
                 AND tc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
                 AND tc.TABLE_NAME = kcu.TABLE_NAME
             WHERE tc.TABLE_SCHEMA = DATABASE()
             AND tc.TABLE_NAME = 'blur_elysium_slides'
             AND tc.CONSTRAINT_TYPE = 'FOREIGN KEY'"
        );

        $fksToDrop = [];
        foreach ($fkConstraints as $fk) {
            if (\in_array($fk['COLUMN_NAME'], $columns, true)) {
                $fksToDrop[] = $fk['CONSTRAINT_NAME'];
            }
        }

        foreach (array_unique($fksToDrop) as $fkName) {
            try {
                $connection->executeStatement(
                    'ALTER TABLE `blur_elysium_slides` DROP FOREIGN KEY `' . $fkName . '`'
                );
            } catch (\Throwable $e) {
                if (preg_match(Defaults::MIGRATION_FK_NOT_EXISTS, $e->getMessage()) !== 1) {
                    throw $e;
                }
            }
        }

        $existingIndexes = $connection->fetchAllAssociative(
            "SELECT INDEX_NAME, COLUMN_NAME
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = 'blur_elysium_slides'"
        );

        $indexesToDrop = [];
        foreach ($existingIndexes as $index) {
            if ($index['INDEX_NAME'] !== 'PRIMARY' && \in_array($index['COLUMN_NAME'], $columns, true)) {
                $indexesToDrop[] = $index['INDEX_NAME'];
            }
        }

        $clauses = [];
        foreach (array_unique($indexesToDrop) as $indexName) {
            $clauses[] = 'DROP INDEX `' . $indexName . '`';
        }

        foreach ($columns as $column) {
            if ($this->hasColumn($connection, 'blur_elysium_slides', $column)) {
                $clauses[] = 'DROP COLUMN `' . $column . '`';
            }
        }

        if ($clauses === []) {
            return;
        }

        try {
            $connection->executeStatement(
                'ALTER TABLE `blur_elysium_slides` ' . implode(', ', $clauses)
            );
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $indexGone = preg_match(Defaults::MIGRATION_INDEX_NOT_EXISTS, $message) === 1;
            $columnGone = preg_match(Defaults::MIGRATION_COLUMN_NOT_EXISTS, $message) === 1;

            if (!$indexGone && !$columnGone) {
                throw $e;
            }

            $this->dropRemainingMediaColumns($connection, $columns);
        }
    }

    /**
     * @return list<string>
     */
    protected function getAvailableTextColumns(Connection $connection): array
    {
        return array_values(array_filter(
            array_keys(self::TEXT_COLUMN_MAP),
            fn (string $column): bool => $this->hasColumn($connection, 'blur_elysium_slides_translation', $column)
        ));
    }

    /**
     * @return list<string>
     */
    protected function getAvailableMediaColumns(Connection $connection): array
    {
        return array_values(array_filter(
            array_keys(self::MEDIA_COLUMN_MAP),
            fn (string $column): bool => $this->hasColumn($connection, 'blur_elysium_slides', $column)
        ));
    }

    protected function hasColumn(Connection $connection, string $table, string $column): bool
    {
        $result = $connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = :table
             AND COLUMN_NAME = :column",
            ['table' => $table, 'column' => $column]
        );

        return (int) $result > 0;
    }

    private function assertContentSettingsAreObjects(Connection $connection): void
    {
        if (!$this->hasColumn($connection, 'blur_elysium_slides_translation', 'content_settings')) {
            return;
        }

        // Do not compare JSON to ''. MySQL native JSON casts the operand via
        // CAST('' AS JSON) and raises ERROR 3141. MariaDB empty strings are
        // already JSON_VALID = 0.
        $where = "
            content_settings IS NOT NULL
            AND CASE
                WHEN JSON_VALID(content_settings) = 0 THEN 1
                WHEN JSON_TYPE(content_settings) = 'OBJECT' THEN 0
                ELSE 1
            END = 1
        ";

        $count = (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM blur_elysium_slides_translation WHERE ' . $where
        );

        if ($count === 0) {
            return;
        }

        $sampleRows = $connection->fetchFirstColumn(
            'SELECT DISTINCT LOWER(HEX(blur_elysium_slides_id))
             FROM blur_elysium_slides_translation
             WHERE ' . $where . '
             LIMIT 5'
        );

        throw new \RuntimeException(\sprintf(
            'Elysium contentSettings migration aborted: %d translation(s) have non-object content_settings. Sample ids: %s. Legacy columns were not dropped.',
            $count,
            implode(', ', $sampleRows)
        ));
    }

    /**
     * @param list<string> $path
     */
    private function jsonSetExpression(string $columnSql, array $path, string $valueSql): string
    {
        $base = 'COALESCE(' . $columnSql . ", '{}')";
        $jsonPath = $this->jsonPath($path);

        if (\count($path) === 1) {
            return 'JSON_SET(' . $base . ", '" . $jsonPath . "', " . $valueSql . ')';
        }

        $parentPath = $this->jsonPath([$path[0]]);
        $coerced = 'CASE
            WHEN JSON_TYPE(JSON_EXTRACT(' . $base . ", '" . $parentPath . "')) = 'OBJECT' THEN " . $base . '
            ELSE JSON_SET(' . $base . ", '" . $parentPath . "', JSON_OBJECT())
        END";

        return 'JSON_SET(' . $coerced . ", '" . $jsonPath . "', " . $valueSql . ')';
    }

    /**
     * @param list<string> $path
     */
    private function jsonPath(array $path): string
    {
        return '$' . implode('', array_map(
            static fn (string $segment): string => '.' . $segment,
            $path
        ));
    }

    /**
     * JSON_UNQUOTE / CAST AS CHAR inherit utf8mb4_general_ci on MySQL, while
     * Shopware tables use utf8mb4_unicode_ci. Comparing those without an
     * explicit collation aborts plugin install (errno 1267).
     */
    private function unicodeText(string $expression): string
    {
        return 'CONVERT(' . $expression . ' USING utf8mb4) COLLATE utf8mb4_unicode_ci';
    }

    /**
     * @return list<string>
     */
    private function translationFailureIdSelects(Connection $connection): array
    {
        $selects = [];

        foreach ($this->getAvailableTextColumns($connection) as $column) {
            $jsonPath = $this->jsonPath(self::TEXT_COLUMN_MAP[$column]);
            $extracted = $this->unicodeText("JSON_UNQUOTE(JSON_EXTRACT(content_settings, '" . $jsonPath . "'))");

            $selects[] = 'SELECT DISTINCT blur_elysium_slides_id AS id
                FROM blur_elysium_slides_translation
                WHERE `' . $column . '` IS NOT NULL AND `' . $column . '` <> \'\'
                AND (
                    JSON_EXTRACT(content_settings, \'' . $jsonPath . '\') IS NULL
                    OR ' . $extracted . ' <> ' . $this->unicodeText('`' . $column . '`') . '
                )';
        }

        return $selects;
    }

    /**
     * @return list<string>
     */
    private function mediaFailureIdSelects(Connection $connection): array
    {
        $availableColumns = $this->getAvailableMediaColumns($connection);
        if ($availableColumns === []) {
            return [];
        }

        $selects = [];
        $mediaWhere = implode(' OR ', array_map(
            static fn (string $column): string => 's.`' . $column . '` IS NOT NULL',
            $availableColumns
        ));
        $selects[] = 'SELECT s.id
            FROM blur_elysium_slides s
            WHERE (' . $mediaWhere . ')
            AND NOT EXISTS (
                SELECT 1 FROM blur_elysium_slides_translation t
                WHERE t.blur_elysium_slides_id = s.id
                AND t.language_id = UNHEX(\'' . ShopwareDefaults::LANGUAGE_SYSTEM . '\')
            )';

        foreach ($availableColumns as $column) {
            $jsonPath = $this->jsonPath(self::MEDIA_COLUMN_MAP[$column]);
            $extracted = $this->unicodeText("JSON_UNQUOTE(JSON_EXTRACT(t.content_settings, '" . $jsonPath . "'))");

            $selects[] = 'SELECT DISTINCT s.id
                FROM blur_elysium_slides s
                INNER JOIN blur_elysium_slides_translation t ON t.blur_elysium_slides_id = s.id
                WHERE s.`' . $column . '` IS NOT NULL
                AND (
                    JSON_EXTRACT(t.content_settings, \'' . $jsonPath . '\') IS NULL
                    OR ' . $extracted . ' <> ' . $this->unicodeText('LOWER(HEX(s.`' . $column . '`))') . '
                )';
        }

        return $selects;
    }

    /**
     * @param list<string> $columns
     */
    private function dropRemainingMediaColumns(Connection $connection, array $columns): void
    {
        $clauses = [];
        foreach ($columns as $column) {
            if ($this->hasColumn($connection, 'blur_elysium_slides', $column)) {
                $clauses[] = 'DROP COLUMN `' . $column . '`';
            }
        }

        if ($clauses === []) {
            return;
        }

        try {
            $connection->executeStatement(
                'ALTER TABLE `blur_elysium_slides` ' . implode(', ', $clauses)
            );
        } catch (\Throwable $e) {
            if (preg_match(Defaults::MIGRATION_COLUMN_NOT_EXISTS, $e->getMessage()) !== 1) {
                throw $e;
            }
        }
    }
}
