<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests\Service\ImportExport;

use Blur\BlurElysiumSlider\Service\ImportExport\SlideImportService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class SlideImportServiceTest extends TestCase
{
    public function testImportCreatesSlides(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $repository->expects($this->once())
            ->method('upsert')
            ->with(
                $this->callback(function (array $payload) {
                    $this->assertCount(1, $payload);
                    $this->assertSame('test-id', $payload[0]['id']);
                    $this->assertSame('product-id', $payload[0]['productId']);
                    $this->assertSame('cover-id', $payload[0]['slideCoverId']);
                    $this->assertSame(['key' => 'value'], $payload[0]['slideSettings']);
                    $this->assertCount(1, $payload[0]['translations']);
                    $this->assertSame('2fbb5fe2e29a4d70aa5854ce7ce3e20b', $payload[0]['translations'][0]['languageId']);
                    $this->assertSame('Test Slide', $payload[0]['translations'][0]['name']);

                    return true;
                }),
                $this->anything()
            );

        $service = new SlideImportService($repository);

        $jsonl = json_encode(['type' => 'elysium-slides-export', 'version' => '1.0', 'count' => 1]) . "\n";
        $jsonl .= json_encode([
            'id' => 'test-id',
            'productId' => 'product-id',
            'slideCoverId' => 'cover-id',
            'slideSettings' => ['key' => 'value'],
            'translations' => [
                '2fbb5fe2e29a4d70aa5854ce7ce3e20b' => [
                    'name' => 'Test Slide',
                    'title' => 'Test Title',
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

        $jsonl = json_encode(['type' => 'elysium-slides-export', 'version' => '1.0', 'count' => 2]) . "\n";
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

        $jsonl = json_encode(['type' => 'elysium-slides-export', 'version' => '1.0', 'count' => 1]) . "\n";
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
