<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Service;

use Blur\BlurElysiumSlider\Service\CmsPageLookup;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

class CmsPageLookupTest extends TestCase
{
    use KernelTestBehaviour;

    private Connection $connection;
    private CmsPageLookup $lookup;

    private array $createdCmsPageIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = $this->getContainer()->get(Connection::class);
        $this->lookup = new CmsPageLookup($this->connection);
    }

    protected function tearDown(): void
    {
        $this->cleanUpTestData();
        parent::tearDown();
    }

    public function testReturnsEmptyArrayForEmptySlideIds(): void
    {
        $result = $this->lookup->getCmsCacheTagsBySlideIds([]);
        static::assertSame([], $result);
    }

    public function testFindsCmsPagesWithBannerSlotMatchingSlideId(): void
    {
        $slideId = Uuid::randomHex();
        $cmsPageId = Uuid::randomHex();

        $this->createBannerChain($cmsPageId, $slideId);

        $result = $this->lookup->getCmsCacheTagsBySlideIds([$slideId]);

        $expectedTag = EntityCacheKeyGenerator::buildCmsTag($cmsPageId);
        static::assertContains($expectedTag, $result);
    }

    public function testFindsCmsPagesWithSliderSlotMatchingSlideIdInCollection(): void
    {
        $slideId1 = Uuid::randomHex();
        $slideId2 = Uuid::randomHex();
        $cmsPageId = Uuid::randomHex();

        $this->createSliderChain($cmsPageId, [$slideId1, $slideId2]);

        $result = $this->lookup->getCmsCacheTagsBySlideIds([$slideId1]);

        $expectedTag = EntityCacheKeyGenerator::buildCmsTag($cmsPageId);
        static::assertContains($expectedTag, $result);
    }

    public function testFindsCmsPagesWhenMatchingSecondSlideInCollection(): void
    {
        $slideId1 = Uuid::randomHex();
        $slideId2 = Uuid::randomHex();
        $cmsPageId = Uuid::randomHex();

        $this->createSliderChain($cmsPageId, [$slideId1, $slideId2]);

        $result = $this->lookup->getCmsCacheTagsBySlideIds([$slideId2]);

        $expectedTag = EntityCacheKeyGenerator::buildCmsTag($cmsPageId);
        static::assertContains($expectedTag, $result);
    }

    public function testFindsCmsPagesWhenMultipleSlideIdsProvided(): void
    {
        $slideIdA = Uuid::randomHex();
        $slideIdB = Uuid::randomHex();
        $cmsPageIdA = Uuid::randomHex();
        $cmsPageIdB = Uuid::randomHex();

        $this->createBannerChain($cmsPageIdA, $slideIdA);
        $this->createBannerChain($cmsPageIdB, $slideIdB);

        $result = $this->lookup->getCmsCacheTagsBySlideIds([$slideIdA, $slideIdB]);

        static::assertCount(2, $result);
        static::assertContains(EntityCacheKeyGenerator::buildCmsTag($cmsPageIdA), $result);
        static::assertContains(EntityCacheKeyGenerator::buildCmsTag($cmsPageIdB), $result);
    }

    public function testReturnsNoTagsWhenNoMatches(): void
    {
        $nonExistentSlideId = Uuid::randomHex();

        $result = $this->lookup->getCmsCacheTagsBySlideIds([$nonExistentSlideId]);

        static::assertSame([], $result);
    }

    public function testReturnsNoTagsWhenSlideIdNotInCollection(): void
    {
        $slideId1 = Uuid::randomHex();
        $slideId2 = Uuid::randomHex();
        $cmsPageId = Uuid::randomHex();

        $this->createSliderChain($cmsPageId, [$slideId1]);

        $result = $this->lookup->getCmsCacheTagsBySlideIds([$slideId2]);

        static::assertSame([], $result);
    }

    public function testReturnsUniqueCacheTags(): void
    {
        $slideId = Uuid::randomHex();
        $cmsPageId = Uuid::randomHex();

        $this->createBannerChain($cmsPageId, $slideId);
        $this->createSliderChain($cmsPageId, [$slideId]);

        $result = $this->lookup->getCmsCacheTagsBySlideIds([$slideId]);

        $expectedTag = EntityCacheKeyGenerator::buildCmsTag($cmsPageId);
        static::assertCount(1, $result);
        static::assertContains($expectedTag, $result);
    }

    public function testFindsCmsPagesBySectionIds(): void
    {
        $cmsPageId = Uuid::randomHex();
        $sectionId = Uuid::randomHex();

        $this->createCmsSection($cmsPageId, $sectionId);

        $result = $this->lookup->getCmsCacheTagsBySectionIds([Uuid::fromHexToBytes($sectionId)]);

        $expectedTag = EntityCacheKeyGenerator::buildCmsTag($cmsPageId);
        static::assertContains($expectedTag, $result);
    }

    public function testReturnsEmptyArrayForEmptySectionIds(): void
    {
        $result = $this->lookup->getCmsCacheTagsBySectionIds([]);
        static::assertSame([], $result);
    }

    public function testReturnsNoTagsForNonExistentSectionId(): void
    {
        $nonExistentSectionId = Uuid::randomHex();

        $result = $this->lookup->getCmsCacheTagsBySectionIds([$nonExistentSectionId]);

        static::assertSame([], $result);
    }

    private function createCmsSection(string $cmsPageId, string $sectionId): void
    {
        $versionId = Uuid::fromHexToBytes(Defaults::LIVE_VERSION);
        $cmsPageIdBytes = Uuid::fromHexToBytes($cmsPageId);
        $sectionIdBytes = Uuid::fromHexToBytes($sectionId);
        $now = (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        $this->connection->insert('cms_page', [
            'id' => $cmsPageIdBytes,
            'version_id' => $versionId,
            'type' => 'landingpage',
            'created_at' => $now,
        ]);

        $this->connection->insert('cms_section', [
            'id' => $sectionIdBytes,
            'version_id' => $versionId,
            'cms_page_version_id' => $versionId,
            'cms_page_id' => $cmsPageIdBytes,
            'position' => 0,
            'type' => 'fullwidth',
            'created_at' => $now,
        ]);

        $this->createdCmsPageIds[] = $cmsPageId;
    }

    private function createBannerChain(
        string $cmsPageId,
        string $slideId
    ): void {
        $versionId = Uuid::fromHexToBytes(Defaults::LIVE_VERSION);
        $cmsPageIdBytes = Uuid::fromHexToBytes($cmsPageId);
        $sectionId = Uuid::randomBytes();
        $blockId = Uuid::randomBytes();
        $slotId = Uuid::randomBytes();
        $now = (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        $this->connection->executeStatement(
            "INSERT IGNORE INTO cms_page (id, version_id, type, created_at) VALUES (:id, :versionId, 'landingpage', :createdAt)",
            ['id' => $cmsPageIdBytes, 'versionId' => $versionId, 'createdAt' => $now]
        );

        $this->connection->insert('cms_section', [
            'id' => $sectionId,
            'version_id' => $versionId,
            'cms_page_version_id' => $versionId,
            'cms_page_id' => $cmsPageIdBytes,
            'position' => 0,
            'type' => 'fullwidth',
            'created_at' => $now,
        ]);

        $this->connection->insert('cms_block', [
            'id' => $blockId,
            'version_id' => $versionId,
            'cms_section_version_id' => $versionId,
            'cms_section_id' => $sectionId,
            'position' => 0,
            'type' => 'default',
            'created_at' => $now,
        ]);

        $this->connection->insert('cms_slot', [
            'id' => $slotId,
            'version_id' => $versionId,
            'cms_block_version_id' => $versionId,
            'cms_block_id' => $blockId,
            'type' => 'blur-elysium-banner',
            'slot' => 'content',
            'created_at' => $now,
        ]);

        $config = json_encode([
            'elysiumSlide' => ['source' => 'static', 'value' => $slideId],
        ], JSON_THROW_ON_ERROR);

        $this->connection->insert('cms_slot_translation', [
            'cms_slot_id' => $slotId,
            'cms_slot_version_id' => $versionId,
            'language_id' => Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM),
            'config' => $config,
            'created_at' => $now,
        ]);

        $this->createdCmsPageIds[] = $cmsPageId;
    }

    private function createSliderChain(
        string $cmsPageId,
        array $slideIds
    ): void {
        $versionId = Uuid::fromHexToBytes(Defaults::LIVE_VERSION);
        $cmsPageIdBytes = Uuid::fromHexToBytes($cmsPageId);
        $sectionId = Uuid::randomBytes();
        $blockId = Uuid::randomBytes();
        $slotId = Uuid::randomBytes();
        $now = (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        $this->connection->executeStatement(
            "INSERT IGNORE INTO cms_page (id, version_id, type, created_at) VALUES (:id, :versionId, 'landingpage', :createdAt)",
            ['id' => $cmsPageIdBytes, 'versionId' => $versionId, 'createdAt' => $now]
        );

        $this->connection->insert('cms_section', [
            'id' => $sectionId,
            'version_id' => $versionId,
            'cms_page_version_id' => $versionId,
            'cms_page_id' => $cmsPageIdBytes,
            'position' => 0,
            'type' => 'fullwidth',
            'created_at' => $now,
        ]);

        $this->connection->insert('cms_block', [
            'id' => $blockId,
            'version_id' => $versionId,
            'cms_section_version_id' => $versionId,
            'cms_section_id' => $sectionId,
            'position' => 0,
            'type' => 'default',
            'created_at' => $now,
        ]);

        $this->connection->insert('cms_slot', [
            'id' => $slotId,
            'version_id' => $versionId,
            'cms_block_version_id' => $versionId,
            'cms_block_id' => $blockId,
            'type' => 'blur-elysium-slider',
            'slot' => 'content',
            'created_at' => $now,
        ]);

        $config = json_encode([
            'elysiumSlideCollection' => ['source' => 'static', 'value' => $slideIds],
        ], JSON_THROW_ON_ERROR);

        $this->connection->insert('cms_slot_translation', [
            'cms_slot_id' => $slotId,
            'cms_slot_version_id' => $versionId,
            'language_id' => Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM),
            'config' => $config,
            'created_at' => $now,
        ]);

        $this->createdCmsPageIds[] = $cmsPageId;
    }

    private function cleanUpTestData(): void
    {
        if (empty($this->createdCmsPageIds)) {
            return;
        }

        $cmsPageIds = array_unique($this->createdCmsPageIds);
        $cmsPageIdBytes = array_map(Uuid::fromHexToBytes(...), $cmsPageIds);
        $versionId = Uuid::fromHexToBytes(Defaults::LIVE_VERSION);

        $this->connection->executeStatement(
            'DELETE FROM cms_slot_translation WHERE cms_slot_id IN (SELECT id FROM cms_slot WHERE cms_block_id IN (SELECT id FROM cms_block WHERE cms_section_id IN (SELECT id FROM cms_section WHERE cms_page_id IN (:cmsPageIds) AND cms_page_version_id = :versionId) AND cms_section_version_id = :versionId) AND cms_block_version_id = :versionId)',
            ['cmsPageIds' => $cmsPageIdBytes, 'versionId' => $versionId],
            ['cmsPageIds' => \Doctrine\DBAL\ArrayParameterType::BINARY]
        );

        $this->connection->executeStatement(
            'DELETE FROM cms_slot WHERE cms_block_id IN (SELECT id FROM cms_block WHERE cms_section_id IN (SELECT id FROM cms_section WHERE cms_page_id IN (:cmsPageIds) AND cms_page_version_id = :versionId) AND cms_section_version_id = :versionId) AND cms_block_version_id = :versionId',
            ['cmsPageIds' => $cmsPageIdBytes, 'versionId' => $versionId],
            ['cmsPageIds' => \Doctrine\DBAL\ArrayParameterType::BINARY]
        );

        $this->connection->executeStatement(
            'DELETE FROM cms_block WHERE cms_section_id IN (SELECT id FROM cms_section WHERE cms_page_id IN (:cmsPageIds) AND cms_page_version_id = :versionId) AND cms_section_version_id = :versionId',
            ['cmsPageIds' => $cmsPageIdBytes, 'versionId' => $versionId],
            ['cmsPageIds' => \Doctrine\DBAL\ArrayParameterType::BINARY]
        );

        $this->connection->executeStatement(
            'DELETE FROM cms_section WHERE cms_page_id IN (:cmsPageIds) AND cms_page_version_id = :versionId',
            ['cmsPageIds' => $cmsPageIdBytes, 'versionId' => $versionId],
            ['cmsPageIds' => \Doctrine\DBAL\ArrayParameterType::BINARY]
        );

        $this->connection->executeStatement(
            'DELETE FROM cms_page WHERE id IN (:cmsPageIds) AND version_id = :versionId',
            ['cmsPageIds' => $cmsPageIdBytes, 'versionId' => $versionId],
            ['cmsPageIds' => \Doctrine\DBAL\ArrayParameterType::BINARY]
        );
    }
}
