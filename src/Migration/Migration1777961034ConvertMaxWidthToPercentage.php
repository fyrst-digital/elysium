<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1777961034ConvertMaxWidthToPercentage extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1777961034;
    }

    public function update(Connection $connection): void
    {
        $this->copyMaxWidthToBasis($connection);
        $this->verifyWithPhp($connection);
    }

    private function copyMaxWidthToBasis(Connection $connection): void
    {
        $paths = [
            ['mobile', 'container'],
            ['mobile', 'content'],
            ['mobile', 'image'],
            ['tablet', 'container'],
            ['tablet', 'content'],
            ['tablet', 'image'],
            ['desktop', 'container'],
            ['desktop', 'content'],
            ['desktop', 'image'],
        ];

        foreach ($paths as [$viewport, $group]) {
            $sourcePath = "\$.viewports.{$viewport}.{$group}.maxWidth";
            $targetPath = "\$.viewports.{$viewport}.{$group}.basis";

            $connection->executeStatement("
                UPDATE `blur_elysium_slides`
                SET `slide_settings` = JSON_SET(
                    COALESCE(`slide_settings`, '{}'),
                    '{$targetPath}',
                    CASE
                        WHEN JSON_EXTRACT(`slide_settings`, '{$sourcePath}') IS NULL THEN 0
                        WHEN JSON_TYPE(JSON_EXTRACT(`slide_settings`, '{$sourcePath}')) = 'NULL' THEN 0
                        WHEN JSON_UNQUOTE(JSON_EXTRACT(`slide_settings`, '{$sourcePath}')) = 'null' THEN 0
                        WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(`slide_settings`, '{$sourcePath}')) AS UNSIGNED) = 0 THEN 0
                        WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(`slide_settings`, '{$sourcePath}')) AS UNSIGNED) > 1000 THEN 100
                        ELSE ROUND(CAST(JSON_UNQUOTE(JSON_EXTRACT(`slide_settings`, '{$sourcePath}')) AS UNSIGNED) / 10)
                    END
                )
            ");
        }
    }

    private function verifyWithPhp(Connection $connection): void
    {
        $rows = $connection->fetchAllAssociative('
            SELECT `id`, `slide_settings`
            FROM `blur_elysium_slides`
        ');

        if (\count($rows) === 0) {
            return;
        }

        foreach ($rows as $row) {
            $settings = \json_decode($row['slide_settings'] ?? '{}', true);

            if (!\is_array($settings) || !isset($settings['viewports']) || !\is_array($settings['viewports'])) {
                continue;
            }

            $modified = false;

            foreach (['mobile', 'tablet', 'desktop'] as $viewport) {
                if (!isset($settings['viewports'][$viewport])) {
                    continue;
                }

                foreach (['container', 'content', 'image'] as $group) {
                    $maxWidth = $settings['viewports'][$viewport][$group]['maxWidth'] ?? null;
                    $basis = $settings['viewports'][$viewport][$group]['basis'] ?? null;

                    if ($basis !== null && \is_numeric($basis)) {
                        continue;
                    }

                    if ($maxWidth === null) {
                        $settings['viewports'][$viewport][$group]['basis'] = 0;
                        $modified = true;
                    } elseif (\is_numeric($maxWidth)) {
                        $intValue = (int) $maxWidth;

                        if ($intValue === 0) {
                            $settings['viewports'][$viewport][$group]['basis'] = 0;
                        } elseif ($intValue > 1000) {
                            $settings['viewports'][$viewport][$group]['basis'] = 100;
                        } else {
                            $settings['viewports'][$viewport][$group]['basis'] = (int) \round($intValue / 10);
                        }
                        $modified = true;
                    }
                }
            }

            if ($modified) {
                $connection->executeStatement('
                    UPDATE `blur_elysium_slides`
                    SET `slide_settings` = :slide_settings
                    WHERE `id` = :id
                ', [
                    'slide_settings' => \json_encode($settings),
                    'id' => $row['id'],
                ]);
            }
        }
    }
}
