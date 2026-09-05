<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Service\ImportExport;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation\ElysiumSlidesTranslationCollection;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation\ElysiumSlidesTranslationEntity;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Blur\BlurElysiumSlider\Service\ImportExport\SlideExportService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

class SlideExportServiceTest extends TestCase
{
    public function testExportReturnsValidJsonl(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $translation = new ElysiumSlidesTranslationEntity();
        $translation->setUniqueIdentifier('trans-id');
        $translation->setLanguageId('2fbb5fe2e29a4d70aa5854ce7ce3e20b');
        $translation->setName('Test Slide');
        $translation->setCustomFields(['foo' => 'bar']);
        $translation->setContentSettings([
            'title' => 'Headline',
            'slideCover' => ['mobileId' => 'cover-id'],
        ]);

        $slide = new ElysiumSlidesEntity();
        $slide->setId('test-id');
        $slide->setProductId('product-id');
        $slide->setSlideSettings(['key' => 'value']);
        $slide->setTranslations(new ElysiumSlidesTranslationCollection([$translation]));

        $collection = new ElysiumSlidesCollection([$slide]);
        $searchResult = new EntitySearchResult(
            'blur_elysium_slides',
            1,
            $collection,
            null,
            new Criteria(),
            Context::createDefaultContext()
        );

        $repository->expects($this->once())
            ->method('search')
            ->willReturn($searchResult);

        $service = new SlideExportService($repository);
        $jsonl = $service->export(['test-id'], Context::createDefaultContext());

        $lines = explode("\n", $jsonl);
        $this->assertCount(2, $lines);

        $header = json_decode($lines[0], true);
        $this->assertSame('elysium-slides-export', $header['type']);
        $this->assertSame('2.0', $header['version']);
        $this->assertSame(1, $header['count']);

        $slideData = json_decode($lines[1], true);
        $this->assertSame('test-id', $slideData['id']);
        $this->assertSame('product-id', $slideData['productId']);
        $this->assertArrayNotHasKey('slideCoverId', $slideData);
        $this->assertSame(['key' => 'value'], $slideData['slideSettings']);
        $this->assertSame('Test Slide', $slideData['translations']['2fbb5fe2e29a4d70aa5854ce7ce3e20b']['name']);
        $this->assertSame(['foo' => 'bar'], $slideData['translations']['2fbb5fe2e29a4d70aa5854ce7ce3e20b']['customFields']);
        $this->assertSame('Headline', $slideData['translations']['2fbb5fe2e29a4d70aa5854ce7ce3e20b']['contentSettings']['title']);
        $this->assertSame('cover-id', $slideData['translations']['2fbb5fe2e29a4d70aa5854ce7ce3e20b']['contentSettings']['slideCover']['mobileId']);
        $this->assertArrayNotHasKey('title', $slideData['translations']['2fbb5fe2e29a4d70aa5854ce7ce3e20b']);
    }

    public function testExportAllReturnsValidJsonl(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $slide = new ElysiumSlidesEntity();
        $slide->setId('test-id');
        $slide->setTranslations(new ElysiumSlidesTranslationCollection());

        $collection = new ElysiumSlidesCollection([$slide]);
        $searchResult = new EntitySearchResult(
            'blur_elysium_slides',
            1,
            $collection,
            null,
            new Criteria(),
            Context::createDefaultContext()
        );

        $repository->expects($this->once())
            ->method('search')
            ->willReturn($searchResult);

        $service = new SlideExportService($repository);
        $jsonl = $service->exportAll(Context::createDefaultContext());

        $lines = explode("\n", $jsonl);
        $this->assertCount(2, $lines);

        $header = json_decode($lines[0], true);
        $this->assertSame('2.0', $header['version']);
        $this->assertSame(1, $header['count']);
    }

    public function testExportWithEmptyIdsReturnsHeaderOnly(): void
    {
        $repository = $this->createStub(EntityRepository::class);

        $service = new SlideExportService($repository);
        $jsonl = $service->export([], Context::createDefaultContext());

        $lines = explode("\n", $jsonl);
        $this->assertCount(1, $lines);

        $header = json_decode($lines[0], true);
        $this->assertSame(0, $header['count']);
        $this->assertSame('2.0', $header['version']);
    }
}
