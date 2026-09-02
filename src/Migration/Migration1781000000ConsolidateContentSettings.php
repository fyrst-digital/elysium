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
    private const CHUNK_SIZE = 100;

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

        $lastId = null;

        while (true) {
            $sql = '
                SELECT s.id
                FROM blur_elysium_slides s
                WHERE (' . $mediaWhere . ')
                AND NOT EXISTS (
                    SELECT 1 FROM blur_elysium_slides_translation t
                    WHERE t.blur_elysium_slides_id = s.id AND t.language_id = :lang_id
                )
            ';
            $params = ['lang_id' => $systemLanguageId];
            $types = ['lang_id' => ParameterType::BINARY];

            if ($lastId !== null) {
                $sql .= ' AND s.id > :lastId';
                $params['lastId'] = $lastId;
                $types['lastId'] = ParameterType::BINARY;
            }

            $sql .= ' ORDER BY s.id ASC LIMIT ' . self::CHUNK_SIZE;

            $slides = $connection->fetchAllAssociative($sql, $params, $types);
            if ($slides === []) {
                break;
            }

            foreach ($slides as $slide) {
                $this->insertSystemLanguageTranslation(
                    $connection,
                    $slide['id'],
                    $systemLanguageId,
                    $now
                );
                $lastId = $slide['id'];
            }
        }
    }

    protected function migrateTranslationData(Connection $connection): void
    {
        $availableColumns = $this->getAvailableTextColumns($connection);
        if ($availableColumns === []) {
            return;
        }

        $selectColumns = implode(', ', array_merge(
            ['blur_elysium_slides_id', 'language_id', 'content_settings'],
            $availableColumns
        ));
        $where = implode(' OR ', array_map(
            static fn (string $column): string => '`' . $column . '` IS NOT NULL',
            $availableColumns
        ));

        $lastSlideId = null;
        $lastLangId = null;

        while (true) {
            $sql = 'SELECT ' . $selectColumns . ' FROM blur_elysium_slides_translation WHERE (' . $where . ')';
            $params = [];
            $types = [];

            if ($lastSlideId !== null && $lastLangId !== null) {
                $sql .= ' AND (
                    blur_elysium_slides_id > :lastSlideId
                    OR (blur_elysium_slides_id = :lastSlideId AND language_id > :lastLangId)
                )';
                $params['lastSlideId'] = $lastSlideId;
                $params['lastLangId'] = $lastLangId;
                $types['lastSlideId'] = ParameterType::BINARY;
                $types['lastLangId'] = ParameterType::BINARY;
            }

            $sql .= ' ORDER BY blur_elysium_slides_id ASC, language_id ASC LIMIT ' . self::CHUNK_SIZE;

            $rows = $connection->fetchAllAssociative($sql, $params, $types);
            if ($rows === []) {
                break;
            }

            foreach ($rows as $row) {
                $this->migrateTranslationRow($connection, $row, $availableColumns);
                $lastSlideId = $row['blur_elysium_slides_id'];
                $lastLangId = $row['language_id'];
            }
        }
    }

    protected function migrateMediaIds(Connection $connection): void
    {
        $availableColumns = $this->getAvailableMediaColumns($connection);
        if ($availableColumns === []) {
            return;
        }

        $selectColumns = implode(', ', array_merge(['id'], array_map(
            static fn (string $column): string => '`' . $column . '`',
            $availableColumns
        )));
        $where = implode(' OR ', array_map(
            static fn (string $column): string => '`' . $column . '` IS NOT NULL',
            $availableColumns
        ));

        $lastId = null;

        while (true) {
            $sql = 'SELECT ' . $selectColumns . ' FROM blur_elysium_slides WHERE (' . $where . ')';
            $params = [];
            $types = [];

            if ($lastId !== null) {
                $sql .= ' AND id > :lastId';
                $params['lastId'] = $lastId;
                $types['lastId'] = ParameterType::BINARY;
            }

            $sql .= ' ORDER BY id ASC LIMIT ' . self::CHUNK_SIZE;

            $slides = $connection->fetchAllAssociative($sql, $params, $types);
            if ($slides === []) {
                break;
            }

            foreach ($slides as $slide) {
                $this->migrateMediaIdsForSlide($connection, $slide, $availableColumns);
                $lastId = $slide['id'];
            }
        }
    }

    protected function verify(Connection $connection): void
    {
        $failures = array_merge(
            $this->verifyTranslationData($connection),
            $this->verifyMediaIds($connection)
        );

        if ($failures === []) {
            return;
        }

        $unique = array_values(array_unique($failures));
        $sample = implode(', ', \array_slice($unique, 0, 5));

        throw new \RuntimeException(\sprintf(
            'Elysium contentSettings migration verification failed for %d slide(s). Sample ids: %s. Legacy columns were not dropped.',
            \count($unique),
            $sample
        ));
    }

    protected function dropTranslationColumns(Connection $connection): void
    {
        foreach (array_keys(self::TEXT_COLUMN_MAP) as $column) {
            if (!$this->hasColumn($connection, 'blur_elysium_slides_translation', $column)) {
                continue;
            }

            try {
                $connection->executeStatement(
                    'ALTER TABLE `blur_elysium_slides_translation` DROP COLUMN `' . $column . '`'
                );
            } catch (\Throwable $e) {
                if (preg_match(Defaults::MIGRATION_COLUMN_NOT_EXISTS, $e->getMessage()) !== 1) {
                    throw $e;
                }
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

        foreach (array_unique($indexesToDrop) as $indexName) {
            try {
                $connection->executeStatement(
                    'ALTER TABLE `blur_elysium_slides` DROP INDEX `' . $indexName . '`'
                );
            } catch (\Throwable $e) {
                if (preg_match(Defaults::MIGRATION_INDEX_NOT_EXISTS, $e->getMessage()) !== 1) {
                    throw $e;
                }
            }
        }

        foreach ($columns as $column) {
            if (!$this->hasColumn($connection, 'blur_elysium_slides', $column)) {
                continue;
            }

            try {
                $connection->executeStatement(
                    'ALTER TABLE `blur_elysium_slides` DROP COLUMN `' . $column . '`'
                );
            } catch (\Throwable $e) {
                if (preg_match(Defaults::MIGRATION_COLUMN_NOT_EXISTS, $e->getMessage()) !== 1) {
                    throw $e;
                }
            }
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

    /**
     * @param array<string, mixed> $row
     * @param list<string> $availableColumns
     */
    private function migrateTranslationRow(Connection $connection, array $row, array $availableColumns): void
    {
        $existing = $this->decodeContentSettings($row['content_settings'] ?? null);
        $changed = false;

        foreach ($availableColumns as $column) {
            $value = $row[$column] ?? null;
            if ($this->isEmptyValue($value)) {
                continue;
            }

            $this->setPath($existing, self::TEXT_COLUMN_MAP[$column], $value);
            $changed = true;
        }

        if (!$changed) {
            return;
        }

        $this->updateContentSettings(
            $connection,
            $row['blur_elysium_slides_id'],
            $row['language_id'],
            $existing
        );
    }

    /**
     * @param array<string, mixed> $slide
     * @param list<string> $availableColumns
     */
    private function migrateMediaIdsForSlide(Connection $connection, array $slide, array $availableColumns): void
    {
        $mediaValues = $this->extractMediaHexValues($slide, $availableColumns);
        if ($mediaValues === []) {
            return;
        }

        $translations = $connection->fetchAllAssociative(
            'SELECT language_id, content_settings FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = :slide_id',
            ['slide_id' => $slide['id']],
            ['slide_id' => ParameterType::BINARY]
        );

        foreach ($translations as $translation) {
            $existing = $this->decodeContentSettings($translation['content_settings'] ?? null);

            foreach ($mediaValues as $column => $hex) {
                $this->setPath($existing, self::MEDIA_COLUMN_MAP[$column], $hex);
            }

            $this->updateContentSettings(
                $connection,
                $slide['id'],
                $translation['language_id'],
                $existing
            );
        }
    }

    /**
     * @return list<string>
     */
    private function verifyTranslationData(Connection $connection): array
    {
        $availableColumns = $this->getAvailableTextColumns($connection);
        if ($availableColumns === []) {
            return [];
        }

        $selectColumns = implode(', ', array_merge(
            ['blur_elysium_slides_id', 'content_settings'],
            $availableColumns
        ));
        $where = implode(' OR ', array_map(
            static fn (string $column): string => '`' . $column . '` IS NOT NULL',
            $availableColumns
        ));

        $rows = $connection->fetchAllAssociative(
            'SELECT ' . $selectColumns . ' FROM blur_elysium_slides_translation WHERE ' . $where
        );

        $failures = [];

        foreach ($rows as $row) {
            $settings = $this->decodeContentSettings($row['content_settings'] ?? null);
            $failed = false;

            foreach ($availableColumns as $column) {
                $value = $row[$column] ?? null;
                if ($this->isEmptyValue($value)) {
                    continue;
                }

                if ($this->getPath($settings, self::TEXT_COLUMN_MAP[$column]) !== $value) {
                    $failed = true;
                    break;
                }
            }

            if ($failed) {
                $failures[] = $this->toHex($row['blur_elysium_slides_id']);
            }
        }

        return $failures;
    }

    /**
     * @return list<string>
     */
    private function verifyMediaIds(Connection $connection): array
    {
        $availableColumns = $this->getAvailableMediaColumns($connection);
        if ($availableColumns === []) {
            return [];
        }

        $selectColumns = implode(', ', array_merge(['id'], array_map(
            static fn (string $column): string => '`' . $column . '`',
            $availableColumns
        )));
        $where = implode(' OR ', array_map(
            static fn (string $column): string => '`' . $column . '` IS NOT NULL',
            $availableColumns
        ));

        $slides = $connection->fetchAllAssociative(
            'SELECT ' . $selectColumns . ' FROM blur_elysium_slides WHERE ' . $where
        );

        $failures = [];

        foreach ($slides as $slide) {
            $mediaValues = $this->extractMediaHexValues($slide, $availableColumns);
            if ($mediaValues === []) {
                continue;
            }

            $translations = $connection->fetchAllAssociative(
                'SELECT language_id, content_settings FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = :slide_id',
                ['slide_id' => $slide['id']],
                ['slide_id' => ParameterType::BINARY]
            );

            $hasSystemLanguage = false;
            $slideFailed = $translations === [];

            foreach ($translations as $translation) {
                if ($this->toHex((string) $translation['language_id']) === ShopwareDefaults::LANGUAGE_SYSTEM) {
                    $hasSystemLanguage = true;
                }

                $settings = $this->decodeContentSettings($translation['content_settings'] ?? null);

                foreach ($mediaValues as $column => $hex) {
                    if ($this->getPath($settings, self::MEDIA_COLUMN_MAP[$column]) !== $hex) {
                        $slideFailed = true;
                        break;
                    }
                }
            }

            if (!$hasSystemLanguage || $slideFailed) {
                $failures[] = $this->toHex($slide['id']);
            }
        }

        return $failures;
    }

    /**
     * @param array<string, mixed> $slide
     * @param list<string> $availableColumns
     * @return array<string, string>
     */
    private function extractMediaHexValues(array $slide, array $availableColumns): array
    {
        $values = [];

        foreach ($availableColumns as $column) {
            $bytes = $slide[$column] ?? null;
            if ($this->isEmptyValue($bytes)) {
                continue;
            }

            if (!\is_string($bytes) || \strlen($bytes) !== 16) {
                throw new \RuntimeException(\sprintf(
                    'Elysium contentSettings migration failed: invalid media UUID on slide %s column %s.',
                    $this->toHex(\is_string($slide['id'] ?? null) ? $slide['id'] : ''),
                    $column
                ));
            }

            try {
                $values[$column] = Uuid::fromBytesToHex($bytes);
            } catch (\Throwable $e) {
                throw new \RuntimeException(\sprintf(
                    'Elysium contentSettings migration failed: invalid media UUID on slide %s column %s.',
                    $this->toHex($slide['id']),
                    $column
                ), 0, $e);
            }
        }

        return $values;
    }

    /**
     * @param array<string, mixed> $contentSettings
     */
    private function updateContentSettings(
        Connection $connection,
        string $slideId,
        string $languageId,
        array $contentSettings
    ): void {
        $connection->executeStatement(
            'UPDATE blur_elysium_slides_translation SET content_settings = :content WHERE blur_elysium_slides_id = :slide_id AND language_id = :lang_id',
            [
                'content' => json_encode($contentSettings, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES),
                'slide_id' => $slideId,
                'lang_id' => $languageId,
            ],
            [
                'content' => ParameterType::STRING,
                'slide_id' => ParameterType::BINARY,
                'lang_id' => ParameterType::BINARY,
            ]
        );
    }

    private function insertSystemLanguageTranslation(
        Connection $connection,
        string $slideId,
        string $systemLanguageId,
        string $createdAt
    ): void {
        $existingName = $connection->fetchOne(
            'SELECT name FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = :slide_id ORDER BY created_at ASC LIMIT 1',
            ['slide_id' => $slideId],
            ['slide_id' => ParameterType::BINARY]
        );

        $name = \is_string($existingName) && $existingName !== ''
            ? $existingName
            : $this->toHex($slideId);

        $connection->insert('blur_elysium_slides_translation', [
            'blur_elysium_slides_id' => $slideId,
            'language_id' => $systemLanguageId,
            'name' => $name,
            'created_at' => $createdAt,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeContentSettings(mixed $raw): array
    {
        if (\is_array($raw)) {
            $decoded = $raw;
        } elseif (!\is_string($raw) || $raw === '') {
            return [];
        } else {
            try {
                $decoded = json_decode($raw, true, 512, \JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                return [];
            }
        }

        if (!\is_array($decoded) || array_is_list($decoded)) {
            return [];
        }

        return $decoded;
    }

    /**
     * @param array<string, mixed> $target
     * @param list<string> $path
     */
    private function setPath(array &$target, array $path, mixed $value): void
    {
        if (\count($path) === 1) {
            $target[$path[0]] = $value;

            return;
        }

        $root = $path[0];
        if (!isset($target[$root]) || !\is_array($target[$root])) {
            $target[$root] = [];
        }

        $target[$root][$path[1]] = $value;
    }

    /**
     * @param array<string, mixed> $source
     * @param list<string> $path
     */
    private function getPath(array $source, array $path): mixed
    {
        $current = $source;

        foreach ($path as $segment) {
            if (!\is_array($current) || !\array_key_exists($segment, $current)) {
                return null;
            }

            $current = $current[$segment];
        }

        return $current;
    }

    private function isEmptyValue(mixed $value): bool
    {
        return $value === null || $value === '';
    }

    private function toHex(string $bytes): string
    {
        try {
            return Uuid::fromBytesToHex($bytes);
        } catch (\Throwable) {
            return bin2hex($bytes);
        }
    }
}
