<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1781000000ConsolidateContentSettings extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1781000000;
    }

    public function update(Connection $connection): void
    {
        // On fresh install, the entity definitions already use contentSettings,
        // so the translation text columns may not exist. Check first.
        if ($this->hasColumn($connection, 'blur_elysium_slides_translation', 'title')) {
            $this->migrateTranslationData($connection);
        }

        if ($this->hasColumn($connection, 'blur_elysium_slides', 'slide_cover_id')) {
            $this->migrateMediaIds($connection);
        }

        if ($this->hasColumn($connection, 'blur_elysium_slides_translation', 'title')) {
            $this->dropTranslationColumns($connection);
        }

        if ($this->hasColumn($connection, 'blur_elysium_slides', 'slide_cover_id')) {
            $this->dropMainTableMediaColumns($connection);
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    private function hasColumn(Connection $connection, string $table, string $column): bool
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

    private function migrateTranslationData(Connection $connection): void
    {
        $rows = $connection->fetchAllAssociative(
            'SELECT blur_elysium_slides_id, language_id, title, description, button_label, url, content_settings
             FROM blur_elysium_slides_translation
             WHERE title IS NOT NULL
                OR description IS NOT NULL
                OR button_label IS NOT NULL
                OR url IS NOT NULL'
        );

        foreach ($rows as $row) {
            $existing = json_decode($row['content_settings'] ?? '{}', true) ?: [];

            if (!empty($row['title'])) {
                $existing['title'] = $row['title'];
            }
            if (!empty($row['description'])) {
                $existing['description'] = $row['description'];
            }
            if (!empty($row['button_label'])) {
                $existing['button']['label'] = $row['button_label'];
            }
            if (!empty($row['url'])) {
                $existing['url'] = $row['url'];
            }

            $connection->executeStatement(
                'UPDATE blur_elysium_slides_translation SET content_settings = :content WHERE blur_elysium_slides_id = :slide_id AND language_id = :lang_id',
                [
                    'content' => json_encode($existing, JSON_THROW_ON_ERROR),
                    'slide_id' => $row['blur_elysium_slides_id'],
                    'lang_id' => $row['language_id'],
                ],
                [
                    'content' => ParameterType::STRING,
                    'slide_id' => ParameterType::BINARY,
                    'lang_id' => ParameterType::BINARY,
                ]
            );
        }
    }

    private function migrateMediaIds(Connection $connection): void
    {
        $slides = $connection->fetchAllAssociative(
            'SELECT id, slide_cover_id, slide_cover_mobile_id, slide_cover_tablet_id, slide_cover_video_id, presentation_media_id
             FROM blur_elysium_slides
             WHERE slide_cover_id IS NOT NULL
                OR slide_cover_mobile_id IS NOT NULL
                OR slide_cover_tablet_id IS NOT NULL
                OR slide_cover_video_id IS NOT NULL
                OR presentation_media_id IS NOT NULL'
        );

        foreach ($slides as $slide) {
            $translations = $connection->fetchAllAssociative(
                'SELECT language_id, content_settings FROM blur_elysium_slides_translation WHERE blur_elysium_slides_id = :slide_id',
                ['slide_id' => $slide['id']],
                ['slide_id' => ParameterType::BINARY]
            );

            foreach ($translations as $translation) {
                $existing = json_decode($translation['content_settings'] ?? '{}', true) ?: [];

                if (!empty($slide['slide_cover_id'])) {
                    $existing['slideCover']['mobileId'] = Uuid::fromBytesToHex($slide['slide_cover_id']);
                }
                if (!empty($slide['slide_cover_mobile_id'])) {
                    $existing['slideCover']['desktopId'] = Uuid::fromBytesToHex($slide['slide_cover_mobile_id']);
                }
                if (!empty($slide['slide_cover_tablet_id'])) {
                    $existing['slideCover']['tabletId'] = Uuid::fromBytesToHex($slide['slide_cover_tablet_id']);
                }
                if (!empty($slide['slide_cover_video_id'])) {
                    $existing['slideCover']['videoId'] = Uuid::fromBytesToHex($slide['slide_cover_video_id']);
                }
                if (!empty($slide['presentation_media_id'])) {
                    $existing['focusImageId'] = Uuid::fromBytesToHex($slide['presentation_media_id']);
                }

                $connection->executeStatement(
                    'UPDATE blur_elysium_slides_translation SET content_settings = :content WHERE blur_elysium_slides_id = :slide_id AND language_id = :lang_id',
                    [
                        'content' => json_encode($existing, JSON_THROW_ON_ERROR),
                        'slide_id' => $slide['id'],
                        'lang_id' => $translation['language_id'],
                    ],
                    [
                        'content' => ParameterType::STRING,
                        'slide_id' => ParameterType::BINARY,
                        'lang_id' => ParameterType::BINARY,
                    ]
                );
            }
        }
    }

    private function dropTranslationColumns(Connection $connection): void
    {
        $drops = [];
        foreach (['title', 'description', 'button_label', 'url'] as $column) {
            if ($this->hasColumn($connection, 'blur_elysium_slides_translation', $column)) {
                $drops[] = 'DROP COLUMN `' . $column . '`';
            }
        }

        if ($drops !== []) {
            $connection->executeStatement(
                'ALTER TABLE blur_elysium_slides_translation ' . implode(', ', $drops)
            );
        }
    }

    private function dropMainTableMediaColumns(Connection $connection): void
    {
        $columns = [
            'slide_cover_id',
            'slide_cover_mobile_id',
            'slide_cover_tablet_id',
            'slide_cover_video_id',
            'presentation_media_id',
        ];

        // Drop foreign key constraints that actually exist
        $fkConstraints = $connection->fetchAllAssociative(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = 'blur_elysium_slides'
             AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
        );

        foreach ($fkConstraints as $fk) {
            $connection->executeStatement(
                'ALTER TABLE blur_elysium_slides DROP FOREIGN KEY `' . $fk['CONSTRAINT_NAME'] . '`'
            );
        }

        // Drop indexes that actually exist for the target columns
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
            $connection->executeStatement(
                'ALTER TABLE blur_elysium_slides DROP INDEX `' . $indexName . '`'
            );
        }

        // Drop columns that still exist
        foreach ($columns as $column) {
            if ($this->hasColumn($connection, 'blur_elysium_slides', $column)) {
                $connection->executeStatement(
                    'ALTER TABLE blur_elysium_slides DROP COLUMN `' . $column . '`'
                );
            }
        }
    }
}
