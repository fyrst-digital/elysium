<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Service\ImportExport;

use Blur\BlurElysiumSlider\Service\ImportExport\SlideImportService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class SlideImportServiceTest extends TestCase
{
    public function testImportCreatesSlidesFromVersion2Payload(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $repository->expects($this->once())
            ->method('upsert')
            ->with(
                $this->callback(function (array $payload) {
                    $this->assertCount(1, $payload);
                    $this->assertSame('test-id', $payload[0]['id']);
                    $this->assertSame('product-id', $payload[0]['productId']);
                    $this->assertArrayNotHasKey('slideCoverId', $payload[0]);
                    $this->assertSame(['key' => 'value'], $payload[0]['slideSettings']);
                    $this->assertCount(1, $payload[0]['translations']);
                    $this->assertSame('2fbb5fe2e29a4d70aa5854ce7ce3e20b', $payload[0]['translations'][0]['languageId']);
                    $this->assertSame('Test Slide', $payload[0]['translations'][0]['name']);
                    $this->assertArrayNotHasKey('title', $payload[0]['translations'][0]);
                    $this->assertSame('Headline', $payload[0]['translations'][0]['contentSettings']['title']);
                    $this->assertSame('cover-id', $payload[0]['translations'][0]['contentSettings']['slideCover']['mobileId']);

                    return true;
                }),
                $this->anything()
            );

        $service = new SlideImportService($repository);

        $jsonl = json_encode(['type' => 'elysium-slides-export', 'version' => '2.0', 'count' => 1]) . "\n";
        $jsonl .= json_encode([
            'id' => 'test-id',
            'productId' => 'product-id',
            'slideSettings' => ['key' => 'value'],
            'translations' => [
                '2fbb5fe2e29a4d70aa5854ce7ce3e20b' => [
                    'name' => 'Test Slide',
                    'contentSettings' => [
                        'title' => 'Headline',
                        'slideCover' => ['mobileId' => 'cover-id'],
                    ],
                ],
            ],
        ]) . "\n";

        $result = $service->import($jsonl, Context::createDefaultContext());

        $this->assertSame(1, $result->getImported());
        $this->assertEmpty($result->getErrors());
    }

    public function testImportMapsLegacyVersion1Payload(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $repository->expects($this->once())
            ->method('upsert')
            ->with(
                $this->callback(function (array $payload) {
                    $this->assertCount(1, $payload);
                    $this->assertSame('test-id', $payload[0]['id']);
                    $this->assertArrayNotHasKey('slideCoverId', $payload[0]);
                    $this->assertArrayNotHasKey('slideCoverMobileId', $payload[0]);
                    $this->assertArrayNotHasKey('presentationMediaId', $payload[0]);

                    $translation = $payload[0]['translations'][0];
                    $this->assertArrayNotHasKey('title', $translation);
                    $this->assertArrayNotHasKey('buttonLabel', $translation);
                    $this->assertSame('Existing Title', $translation['contentSettings']['title']);
                    $this->assertSame('From column', $translation['contentSettings']['description']);
                    $this->assertSame('/path', $translation['contentSettings']['url']);
                    $this->assertSame('Click me', $translation['contentSettings']['button']['label']);
                    $this->assertSame('desktop-cover', $translation['contentSettings']['slideCover']['desktopId']);
                    $this->assertSame('mobile-cover', $translation['contentSettings']['slideCover']['mobileId']);
                    $this->assertSame('tablet-cover', $translation['contentSettings']['slideCover']['tabletId']);
                    $this->assertSame('video-id', $translation['contentSettings']['slideCover']['videoId']);
                    $this->assertSame('focus-id', $translation['contentSettings']['focusImageId']);

                    return true;
                }),
                $this->anything()
            );

        $service = new SlideImportService($repository);

        $jsonl = json_encode(['type' => 'elysium-slides-export', 'version' => '1.0', 'count' => 1]) . "\n";
        $jsonl .= json_encode([
            'id' => 'test-id',
            'slideCoverId' => 'desktop-cover',
            'slideCoverMobileId' => 'mobile-cover',
            'slideCoverTabletId' => 'tablet-cover',
            'slideCoverVideoId' => 'video-id',
            'presentationMediaId' => 'focus-id',
            'translations' => [
                '2fbb5fe2e29a4d70aa5854ce7ce3e20b' => [
                    'name' => 'Test Slide',
                    'title' => 'Column Title',
                    'description' => 'From column',
                    'buttonLabel' => 'Click me',
                    'url' => '/path',
                    'contentSettings' => [
                        'title' => 'Existing Title',
                    ],
                ],
            ],
        ]) . "\n";

        $result = $service->import($jsonl, Context::createDefaultContext());

        $this->assertSame(1, $result->getImported());
        $this->assertEmpty($result->getErrors());
    }

    public function testImportWithInvalidHeaderReturnsError(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->never())->method('upsert');

        $service = new SlideImportService($repository);

        $result = $service->import('invalid', Context::createDefaultContext());

        $this->assertSame(0, $result->getImported());
        $this->assertNotEmpty($result->getErrors());
    }

    public function testImportWithEmptyContentReturnsError(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->never())->method('upsert');

        $service = new SlideImportService($repository);

        $result = $service->import("", Context::createDefaultContext());

        $this->assertSame(0, $result->getImported());
        $this->assertNotEmpty($result->getErrors());
    }

    public function testImportSkipsInvalidLines(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $repository->expects($this->once())
            ->method('upsert')
            ->with(
                $this->callback(function (array $payload) {
                    $this->assertCount(1, $payload);
                    $this->assertSame('valid-id', $payload[0]['id']);

                    return true;
                }),
                $this->anything()
            );

        $service = new SlideImportService($repository);

        $jsonl = json_encode(['type' => 'elysium-slides-export', 'version' => '2.0', 'count' => 2]) . "\n";
        $jsonl .= "invalid json\n";
        $jsonl .= json_encode([
            'id' => 'valid-id',
            'translations' => [
                '2fbb5fe2e29a4d70aa5854ce7ce3e20b' => [
                    'name' => 'Valid Slide',
                ],
            ],
        ]) . "\n";

        $result = $service->import($jsonl, Context::createDefaultContext());

        $this->assertSame(1, $result->getImported());
        $this->assertCount(1, $result->getErrors());
    }

    public function testImportSkipsMissingId(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->never())->method('upsert');

        $service = new SlideImportService($repository);

        $jsonl = json_encode(['type' => 'elysium-slides-export', 'version' => '2.0', 'count' => 1]) . "\n";
        $jsonl .= json_encode([
            'translations' => [
                '2fbb5fe2e29a4d70aa5854ce7ce3e20b' => [
                    'name' => 'Slide Without ID',
                ],
            ],
        ]) . "\n";

        $result = $service->import($jsonl, Context::createDefaultContext());

        $this->assertSame(0, $result->getImported());
        $this->assertNotEmpty($result->getErrors());
    }
}
