<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Demodata;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Defaults;
use Doctrine\DBAL\Connection;
use Maltyxx\ImagesGenerator\ImagesGeneratorProvider;
use Shopware\Core\Content\Media\File\FileNameProvider;
use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityWriterInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Shopware\Core\Framework\Demodata\DemodataContext;
use Shopware\Core\Framework\Demodata\DemodataGeneratorInterface;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Util\Hasher;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
#[Package('fundamentals@after-sales')]
class ElysiumSlidesGenerator implements DemodataGeneratorInterface
{
    private const BANNER_BUTTON_LABELS = [
        'Shop now', 'Discover more', 'View details', 'Learn more', 'Get yours',
    ];

    /**
     * @internal
     */
    public function __construct(
        private readonly EntityWriterInterface $writer,
        private readonly FileSaver $fileSaver,
        private readonly FileNameProvider $fileNameProvider,
        private readonly ElysiumSlidesDefinition $elysiumSlidesDefinition,
        private readonly MediaDefinition $mediaDefinition,
        private readonly Connection $connection,
    ) {
    }

    public function getDefinition(): string
    {
        return ElysiumSlidesDefinition::class;
    }

    public function generate(int $numberOfItems, DemodataContext $context, array $options = []): void
    {
        $this->ensureDependencies();

        $context->getConsole()->progressStart($numberOfItems);

        $writeContext = WriteContext::createFromContext($context->getContext());

        $payload = [];
        for ($i = 0; $i < $numberOfItems; ++$i) {
            $payload[] = $this->buildSlidePayload($context, $i + 1);
        }

        foreach (array_chunk($payload, 100) as $chunk) {
            $this->writer->upsert($this->elysiumSlidesDefinition, $chunk, $writeContext);
            $context->getConsole()->progressAdvance(\count($chunk));
        }

        $context->getConsole()->progressFinish();
    }

    /**
     * @internal
     *
     * @return array<string, mixed>
     */
    public function buildSlidePayload(DemodataContext $context, int $index): array
    {
        $coverMediaId = $this->createPlaceholderMedia($context, sprintf('Elysium Slide #%d', $index), 1920, 600);
        $mobileCoverMediaId = $this->createPlaceholderMedia($context, sprintf('Elysium Slide #%d (Mobile)', $index), 800, 1200);

        return $this->assembleSlidePayload($context, $index, $coverMediaId, $mobileCoverMediaId);
    }

    /**
     * @internal
     *
     * @return array<string, mixed>
     */
    public function assembleSlidePayload(DemodataContext $context, int $index, string $coverMediaId, string $mobileCoverMediaId): array
    {
        $faker = $context->getFaker();
        $console = $context->getConsole();

        $productId = $this->pickRandomId('product', $console, 'No products found. Slides will be created without product links.');
        $categoryId = $this->pickRandomId('category', $console, 'No categories found. Slides will be created without category links.');

        if ($productId !== null) {
            $linkingType = 'product';
        } elseif ($categoryId !== null) {
            $linkingType = 'category';
        } else {
            $linkingType = 'url';
        }

        return [
            'id' => Uuid::randomHex(),
            'name' => sprintf('Demo Slide %d', $index),
            'title' => $faker->words(5, true),
            'description' => $faker->text(200),
            'buttonLabel' => $faker->randomElement(self::BANNER_BUTTON_LABELS),
            'url' => $faker->url(),
            'productId' => $productId,
            'categoryId' => $categoryId,
            'slideCoverId' => $coverMediaId,
            'slideCoverMobileId' => $mobileCoverMediaId,
            'slideCoverTabletId' => $coverMediaId,
            'slideCoverVideoId' => null,
            'presentationMediaId' => null,
            'activeFrom' => null,
            'activeUntil' => null,
            'slideSettings' => $this->buildSlideSettings($linkingType, $productId, $categoryId),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSlideSettings(string $linkingType, ?string $productId, ?string $categoryId): array
    {
        $linking = [
            'type' => $linkingType,
            'overlay' => false,
            'openExternal' => false,
            'showProductTitle' => $productId !== null,
            'showProductDescription' => $productId !== null,
            'showProductFocusImage' => $productId !== null,
            'showCategoryTitle' => $categoryId !== null,
            'showCategoryDescription' => $categoryId !== null,
            'showCategoryFocusImage' => $categoryId !== null,
        ];

        return [
            'slide' => [
                'linking' => $linking,
                'cssClass' => '',
                'bgColor' => '#ffffff',
                'bgGradient' => [
                    'startColor' => '',
                    'endColor' => '',
                    'gradientDeg' => 90,
                ],
                'headline' => [
                    'element' => 'h2',
                    'color' => '#1a1a1a',
                ],
                'description' => [
                    'color' => '#1a1a1a',
                ],
            ],
            'container' => [
                'bgColor' => '',
            ],
            'viewports' => Defaults::cmsSectionSettings()[Defaults::CMS_SECTION_SETTINGS_KEY]['viewports'],
        ];
    }

    private function createPlaceholderMedia(DemodataContext $context, string $label, int $width, int $height): string
    {
        $mediaId = Uuid::randomHex();

        $imagePath = ImagesGeneratorProvider::imageGenerator(
            null,
            $width,
            $height,
            'jpg',
            true,
            $label,
            '#d8dde6',
            '#1a1a1a'
        );

        $this->writer->insert(
            $this->mediaDefinition,
            [[
                'id' => $mediaId,
                'title' => $label,
                'mediaFolderId' => Defaults::MEDIA_FOLDER_ID,
                'private' => false,
            ]],
            WriteContext::createFromContext($context->getContext())
        );

        $this->fileSaver->persistFileToMedia(
            new MediaFile(
                $imagePath,
                (string) mime_content_type($imagePath),
                pathinfo($imagePath, \PATHINFO_EXTENSION),
                (int) filesize($imagePath),
                Hasher::hashFile($imagePath, 'md5')
            ),
            $this->fileNameProvider->provide(
                pathinfo($imagePath, \PATHINFO_FILENAME),
                pathinfo($imagePath, \PATHINFO_EXTENSION),
                $mediaId,
                $context->getContext()
            ),
            $mediaId,
            $context->getContext()
        );

        return $mediaId;
    }

    private function pickRandomId(string $table, \Symfony\Component\Console\Style\SymfonyStyle $console, string $emptyMessage): ?string
    {
        $ids = $this->connection->fetchFirstColumn(
            sprintf('SELECT LOWER(HEX(id)) FROM %s ORDER BY RAND() LIMIT 1', $table)
        );

        if ($ids === []) {
            $console->warning($emptyMessage);

            return null;
        }

        return (string) $ids[0];
    }

    private function ensureDependencies(): void
    {
        if (!class_exists(ImagesGeneratorProvider::class)) {
            throw new \RuntimeException(
                'Please install composer package "shopware/dev-tools" to use the Elysium demodata command.'
            );
        }
    }
}
