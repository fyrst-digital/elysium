<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Service\ImportExport;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class SlideImportService
{
    public function __construct(
        private readonly EntityRepository $slideRepository
    ) {
    }

    public function import(string $jsonlContent, Context $context): ImportResult
    {
        $lines = array_filter(
            explode("\n", $jsonlContent),
            fn (string $line): bool => trim($line) !== ''
        );

        if (empty($lines)) {
            return new ImportResult(0, ['The import file is empty.']);
        }

        // Validate header
        $header = json_decode(array_shift($lines), true);
        if (!is_array($header) || !isset($header['type']) || $header['type'] !== 'elysium-slides-export') {
            return new ImportResult(0, ['Invalid import file format. Expected an Elysium slides export.']);
        }

        $result = new ImportResult();
        $payload = [];

        foreach ($lines as $index => $line) {
            $slideData = json_decode($line, true);

            if (!is_array($slideData)) {
                $result = $result->addError(sprintf('Line %d: Invalid JSON.', $index + 2));
                continue;
            }

            $slidePayload = $this->buildSlidePayload($slideData);
            if ($slidePayload === null) {
                $result = $result->addError(sprintf('Line %d: Missing required fields (id or name).', $index + 2));
                continue;
            }

            $payload[] = $slidePayload;
            $result = $result->addImported();
        }

        if (!empty($payload)) {
            $this->slideRepository->upsert($payload, $context);
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    private function buildSlidePayload(array $data): ?array
    {
        if (empty($data['id'])) {
            return null;
        }

        $payload = [
            'id' => $data['id'],
            'productId' => $data['productId'] ?? null,
            'categoryId' => $data['categoryId'] ?? null,
            'activeFrom' => $data['activeFrom'] ?? null,
            'activeUntil' => $data['activeUntil'] ?? null,
            'slideSettings' => $data['slideSettings'] ?? null,
        ];

        if (!empty($data['translations']) && is_array($data['translations'])) {
            $payload['translations'] = [];

            foreach ($data['translations'] as $languageId => $translation) {
                if (!is_array($translation)) {
                    continue;
                }

                $payload['translations'][] = [
                    'languageId' => $languageId,
                    'name' => $translation['name'] ?? null,
                    'customFields' => $translation['customFields'] ?? null,
                    'contentSettings' => $this->buildContentSettings($data, $translation),
                ];
            }
        }

        if (empty($payload['translations'])) {
            return null;
        }

        $hasName = false;
        foreach ($payload['translations'] as $translation) {
            if (!empty($translation['name'])) {
                $hasName = true;
                break;
            }
        }

        if (!$hasName) {
            return null;
        }

        return $payload;
    }

    /**
     * Builds contentSettings for a translation.
     *
     * Version 2.0 files already store copy and media IDs here. Version 1.0 files
     * from master keep title/description/url/buttonLabel on the translation and
     * media FKs on the slide root; those are mapped with the same rules as
     * Migration1781000000ConsolidateContentSettings, filling only empty keys.
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $translation
     * @return array<string, mixed>
     */
    private function buildContentSettings(array $data, array $translation): array
    {
        $contentSettings = is_array($translation['contentSettings'] ?? null)
            ? $translation['contentSettings']
            : [];

        $this->fillEmpty($contentSettings, 'title', $translation['title'] ?? null);
        $this->fillEmpty($contentSettings, 'description', $translation['description'] ?? null);
        $this->fillEmpty($contentSettings, 'url', $translation['url'] ?? null);

        if (!$this->isEmptyValue($translation['buttonLabel'] ?? null)) {
            if (!isset($contentSettings['button']) || !is_array($contentSettings['button'])) {
                $contentSettings['button'] = [];
            }
            $this->fillEmpty($contentSettings['button'], 'label', $translation['buttonLabel']);
        }

        $slideCover = is_array($contentSettings['slideCover'] ?? null)
            ? $contentSettings['slideCover']
            : [];

        $this->fillEmpty($slideCover, 'desktopId', $data['slideCoverId'] ?? null);
        $this->fillEmpty($slideCover, 'mobileId', $data['slideCoverMobileId'] ?? null);
        $this->fillEmpty($slideCover, 'tabletId', $data['slideCoverTabletId'] ?? null);
        $this->fillEmpty($slideCover, 'videoId', $data['slideCoverVideoId'] ?? null);

        if ($slideCover !== []) {
            $contentSettings['slideCover'] = $slideCover;
        }

        $this->fillEmpty($contentSettings, 'focusImageId', $data['presentationMediaId'] ?? null);

        return $contentSettings;
    }

    /**
     * @param array<string, mixed> $target
     */
    private function fillEmpty(array &$target, string $key, mixed $value): void
    {
        if ($this->isEmptyValue($value) || !$this->isEmptyValue($target[$key] ?? null)) {
            return;
        }

        $target[$key] = $value;
    }

    private function isEmptyValue(mixed $value): bool
    {
        return $value === null || $value === '';
    }
}
