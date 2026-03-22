<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Migration\Migration1707906587ChangeMediaToSlideCover;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1707906587ChangeMediaToSlideCoverTest extends AbstractMigrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSlideTableWithMediaColumns();
    }

    public function testMigrationRenamesMediaIdToSlideCoverId(): void
    {
        $migration = new Migration1707906587ChangeMediaToSlideCover();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'slide_cover_id', 'binary', true);
    }

    public function testMigrationRenamesMediaPortraitIdToSlideCoverMobileId(): void
    {
        $migration = new Migration1707906587ChangeMediaToSlideCover();
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'slide_cover_mobile_id', 'binary', true);
    }

    public function testOldMediaIdColumnDoesNotExist(): void
    {
        $migration = new Migration1707906587ChangeMediaToSlideCover();
        $this->runMigration($migration);

        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'media_id']
        );

        static::assertFalse($column, 'Old media_id column should not exist (renamed to slide_cover_id)');
    }

    public function testOldMediaPortraitIdColumnDoesNotExist(): void
    {
        $migration = new Migration1707906587ChangeMediaToSlideCover();
        $this->runMigration($migration);

        $column = $this->connection->fetchAssociative(
            'SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()',
            ['blur_elysium_slides', 'media_portrait_id']
        );

        static::assertFalse($column, 'Old media_portrait_id column should not exist (renamed to slide_cover_mobile_id)');
    }

    public function testMigrationIsIdempotent(): void
    {
        $migration = new Migration1707906587ChangeMediaToSlideCover();

        $this->runMigration($migration);
        $this->runMigration($migration);

        $this->assertColumnExists('blur_elysium_slides', 'slide_cover_id');
        $this->assertColumnExists('blur_elysium_slides', 'slide_cover_mobile_id');
    }

    public function testMigrationPreservesExistingData(): void
    {
        $mediaId = Uuid::randomBytes();
        $mediaPortraitId = Uuid::randomBytes();

        $slideId = $this->insertSlide([
            'media_id' => $mediaId,
            'media_portrait_id' => $mediaPortraitId,
        ]);

        $migration = new Migration1707906587ChangeMediaToSlideCover();
        $this->runMigration($migration);

        $slide = $this->connection->fetchAssociative(
            'SELECT slide_cover_id, slide_cover_mobile_id FROM blur_elysium_slides WHERE id = ?',
            [$slideId]
        );

        static::assertEquals($mediaId, $slide['slide_cover_id'], 'media_id data should be preserved in slide_cover_id');
        static::assertEquals($mediaPortraitId, $slide['slide_cover_mobile_id'], 'media_portrait_id data should be preserved in slide_cover_mobile_id');
    }
}
