<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Service\ImportExport;

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

        $slide = $this->createMock(ElysiumSlidesEntity::class);
        $slide->method('getId')->willReturn('test-id');
        $slide->method('getProductId')->willReturn('product-id');
        $slide->method('getCategoryId')->willReturn(null);
        $slide->method('getSlideCoverId')->willReturn('cover-id');
        $slide->method('getSlideCoverMobileId')->willReturn(null);
        $slide->method('getSlideCoverTabletId')->willReturn(null);
        $slide->method('getSlideCoverVideoId')->willReturn(null);
        $slide->method('getPresentationMediaId')->willReturn(null);
        $slide->method('getActiveFrom')->willReturn(null);
        $slide->method('getActiveUntil')->willReturn(null);
        $slide->method('getSlideSettings')->willReturn(['key' => 'value']);
        $slide->method('getTranslations')->willReturn(null);

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
        $this->assertSame('1.0', $header['version']);
        $this->assertSame(1, $header['count']);

        $slideData = json_decode($lines[1], true);
        $this->assertSame('test-id', $slideData['id']);
        $this->assertSame('product-id', $slideData['productId']);
        $this->assertSame('cover-id', $slideData['slideCoverId']);
        $this->assertSame(['key' => 'value'], $slideData['slideSettings']);
    }

    public function testExportAllReturnsValidJsonl(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $slide = $this->createMock(ElysiumSlidesEntity::class);
        $slide->method('getId')->willReturn('test-id');
        $slide->method('getProductId')->willReturn(null);
        $slide->method('getCategoryId')->willReturn(null);
        $slide->method('getSlideCoverId')->willReturn(null);
        $slide->method('getSlideCoverMobileId')->willReturn(null);
        $slide->method('getSlideCoverTabletId')->willReturn(null);
        $slide->method('getSlideCoverVideoId')->willReturn(null);
        $slide->method('getPresentationMediaId')->willReturn(null);
        $slide->method('getActiveFrom')->willReturn(null);
        $slide->method('getActiveUntil')->willReturn(null);
        $slide->method('getSlideSettings')->willReturn(null);
        $slide->method('getTranslations')->willReturn(null);

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
        $this->assertSame(1, $header['count']);
    }

    public function testExportWithEmptyIdsReturnsHeaderOnly(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $service = new SlideExportService($repository);
        $jsonl = $service->export([], Context::createDefaultContext());

        $lines = explode("\n", $jsonl);
        $this->assertCount(1, $lines);

        $header = json_decode($lines[0], true);
        $this->assertSame(0, $header['count']);
    }
}
