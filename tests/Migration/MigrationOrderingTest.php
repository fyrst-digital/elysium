<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use PHPUnit\Framework\TestCase;

class MigrationOrderingTest extends TestCase
{
    public function testMigrationsAreInChronologicalOrder(): void
    {
        $migrationDir = __DIR__ . '/../../src/Migration';
        $files = glob($migrationDir . '/Migration*.php');

        static::assertNotEmpty($files, 'Migration files should exist');

        $timestamps = [];
        foreach ($files as $file) {
            $filename = basename($file, '.php');
            // Extract timestamp from Migration<TIMESTAMP>Name.php
            if (preg_match('/Migration(\d{10})/', $filename, $matches)) {
                $timestamps[] = (int) $matches[1];
            }
        }

        static::assertNotEmpty($timestamps, 'Should find timestamps in migration filenames');

        // Check for duplicates
        $uniqueTimestamps = array_unique($timestamps);
        static::assertCount(
            count($timestamps),
            $uniqueTimestamps,
            'Migration timestamps should be unique. Duplicates found: ' .
            implode(', ', array_diff_key($timestamps, $uniqueTimestamps))
        );

        // Check chronological order
        $sortedTimestamps = $timestamps;
        sort($sortedTimestamps);
        static::assertSame(
            $sortedTimestamps,
            $timestamps,
            'Migrations should be in chronological order. ' .
            'Expected order: ' . implode(', ', $sortedTimestamps) .
            '. Actual order: ' . implode(', ', $timestamps)
        );
    }
}
