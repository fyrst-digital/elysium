<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Service;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation\ElysiumSlidesTranslationCollection;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation\ElysiumSlidesTranslationEntity;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Blur\BlurElysiumSlider\Service\SlideCoverImageSwitchService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

class SlideCoverImageSwitchServiceTest extends TestCase
{
    public function testSwitchMovesDesktopCoverToMobileWhenMobileIsMissing(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $slide = $this->createSlide(
            'slide-id',
            [
                $this->createTranslation('lang-id', [
                    'slideCover' => [
                        'desktopId' => 'cover-id',
                        'tabletId' => 'tablet-id',
                    ],
                ]),
            ]
        );

        $searchResult = $this->createSearchResult(new ElysiumSlidesCollection([$slide]));

        $repository->expects($this->once())
            ->method('search')
            ->with(
                $this->callback(function (Criteria $criteria): bool {
                    $this->assertTrue($criteria->hasAssociation('translations'));
                    $this->assertSame(100, $criteria->getLimit());
                    $this->assertSame(0, $criteria->getOffset());

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
                        'translations' => [
                            [
                                'languageId' => 'lang-id',
                                'contentSettings' => [
                                    'slideCover' => [
                                        'desktopId' => null,
                                        'tabletId' => 'tablet-id',
                                        'mobileId' => 'cover-id',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                $this->isInstanceOf(Context::class)
            );

        $service = new SlideCoverImageSwitchService($repository);
        $affected = $service->switch(Context::createDefaultContext());

        $this->assertSame(1, $affected);
    }

    public function testSwitchSkipsTranslationsThatAlreadyHaveMobileCover(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $slide = $this->createSlide(
            'slide-id',
            [
                $this->createTranslation('lang-id', [
                    'slideCover' => [
                        'mobileId' => 'mobile-id',
                        'desktopId' => 'cover-id',
                    ],
                ]),
            ]
        );

        $repository->expects($this->once())
            ->method('search')
            ->willReturn($this->createSearchResult(new ElysiumSlidesCollection([$slide])));

        $repository->expects($this->never())
            ->method('upsert');

        $service = new SlideCoverImageSwitchService($repository);
        $affected = $service->switch(Context::createDefaultContext());

        $this->assertSame(0, $affected);
    }

    public function testSwitchUpdatesOnlyMatchingTranslationsAndCountsSlideOnce(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $slide = $this->createSlide(
            'slide-id',
            [
                $this->createTranslation('de-id', [
                    'slideCover' => [
                        'desktopId' => 'de-cover',
                    ],
                ]),
                $this->createTranslation('en-id', [
                    'slideCover' => [
                        'mobileId' => 'en-mobile',
                        'desktopId' => 'en-cover',
                    ],
                ]),
            ]
        );

        $repository->expects($this->once())
            ->method('search')
            ->willReturn($this->createSearchResult(new ElysiumSlidesCollection([$slide])));

        $repository->expects($this->once())
            ->method('upsert')
            ->with(
                $this->callback(function (array $payload): bool {
                    $this->assertCount(1, $payload);
                    $this->assertSame('slide-id', $payload[0]['id']);
                    $this->assertCount(1, $payload[0]['translations']);
                    $this->assertSame('de-id', $payload[0]['translations'][0]['languageId']);
                    $this->assertSame('de-cover', $payload[0]['translations'][0]['contentSettings']['slideCover']['mobileId']);
                    $this->assertNull($payload[0]['translations'][0]['contentSettings']['slideCover']['desktopId']);

                    return true;
                }),
                $this->isInstanceOf(Context::class)
            );

        $service = new SlideCoverImageSwitchService($repository);
        $affected = $service->switch(Context::createDefaultContext());

        $this->assertSame(1, $affected);
    }

    public function testSwitchReturnsZeroWhenNoSlidesMatch(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $repository->expects($this->once())
            ->method('search')
            ->willReturn($this->createSearchResult(new ElysiumSlidesCollection([])));

        $repository->expects($this->never())
            ->method('upsert');

        $service = new SlideCoverImageSwitchService($repository);
        $affected = $service->switch(Context::createDefaultContext());

        $this->assertSame(0, $affected);
    }

    /**
     * @param list<ElysiumSlidesTranslationEntity> $translations
     */
    private function createSlide(string $id, array $translations): ElysiumSlidesEntity
    {
        $slide = new ElysiumSlidesEntity();
        $slide->setId($id);
        $slide->setTranslations(new ElysiumSlidesTranslationCollection($translations));

        return $slide;
    }

    /**
     * @param array<string, mixed> $contentSettings
     */
    private function createTranslation(string $languageId, array $contentSettings): ElysiumSlidesTranslationEntity
    {
        $translation = new ElysiumSlidesTranslationEntity();
        $translation->setUniqueIdentifier($languageId);
        $translation->setLanguageId($languageId);
        $translation->setContentSettings($contentSettings);

        return $translation;
    }

    private function createSearchResult(ElysiumSlidesCollection $collection): EntitySearchResult
    {
        return new EntitySearchResult(
            'blur_elysium_slides',
            $collection->count(),
            $collection,
            null,
            new Criteria(),
            Context::createDefaultContext()
        );
    }
}
