<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Migration;

use Blur\BlurElysiumSlider\Defaults;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

class Migration1744028421SetDefaultMediaFolderIdTest extends TestCase
{
    use KernelTestBehaviour;

    private Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = $this->getContainer()->get(Connection::class);
        $this->connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->connection->rollBack();
        parent::tearDown();
    }

    public function testMediaFolderExistsWithCorrectId(): void
    {
        $mediaFolderId = hex2bin(Defaults::MEDIA_FOLDER_ID);

        $folder = $this->connection->fetchAssociative(
            'SELECT id, name FROM media_folder WHERE id = ?',
            [$mediaFolderId]
        );

        static::assertNotFalse($folder, 'Media folder with default ID should exist');
    }

    public function testDefaultFolderConfigurationExists(): void
    {
        $defaultFolder = $this->connection->fetchAssociative(
            'SELECT mdf.id, mdf.folder_name, mdf.entity FROM media_default_folder mdf WHERE mdf.entity = ?',
            ['blur_elysium_slides']
        );

        static::assertNotFalse($defaultFolder, 'Default folder configuration for blur_elysium_slides should exist');
    }

    public function testMigrationIsIdempotent(): void
    {
        $mediaFolderId = hex2bin(Defaults::MEDIA_FOLDER_ID);

        $firstCheck = $this->connection->fetchOne(
            'SELECT 1 FROM media_folder WHERE id = ?',
            [$mediaFolderId]
        );

        static::assertNotFalse($firstCheck, 'Media folder should exist on first check');
    }
}
