<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Service;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Blur\BlurElysiumSlider\Service\SlideCoverImageSwitchService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;

class SlideCoverImageSwitchServiceTest extends TestCase
{
    public function testSwitchMovesDesktopCoverToMobileWhenMobileIsMissing(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $slide = $this->createMock(ElysiumSlidesEntity::class);
        $slide->method('getId')->willReturn('slide-id');
        $slide->method('getSlideCoverId')->willReturn('cover-id');

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
            ->with(
                $this->callback(function (Criteria $criteria): bool {
                    $filters = $criteria->getFilters();
                    $this->assertCount(2, $filters);
                    $this->assertInstanceOf(EqualsFilter::class, $filters[0]);
                    $this->assertSame('slideCoverMobileId', $filters[0]->getField());
                    $this->assertNull($filters[0]->getValue());
                    $this->assertInstanceOf(NotFilter::class, $filters[1]);

                    return true;
                }),
                $this->isInstanceOf(Context::class)
            )
            ->willReturn($searchResult);

        $repository->expects($this->once())
            ->method('upsert')
            ->with(
                [
                    [
                        'id' => 'slide-id',
                        'slideCoverMobileId' => 'cover-id',
                        'slideCoverId' => null,
                    ],
                ],
                $this->isInstanceOf(Context::class)
            );

        $service = new SlideCoverImageSwitchService($repository);
        $affected = $service->switch(Context::createDefaultContext());

        $this->assertSame(1, $affected);
    }

    public function testSwitchReturnsZeroWhenNoSlidesMatch(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $searchResult = new EntitySearchResult(
            'blur_elysium_slides',
            0,
            new ElysiumSlidesCollection([]),
            null,
            new Criteria(),
            Context::createDefaultContext()
        );

        $repository->expects($this->once())
            ->method('search')
            ->willReturn($searchResult);

        $repository->expects($this->never())
            ->method('upsert');

        $service = new SlideCoverImageSwitchService($repository);
        $affected = $service->switch(Context::createDefaultContext());

        $this->assertSame(0, $affected);
    }
}
