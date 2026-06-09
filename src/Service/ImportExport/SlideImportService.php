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

    private function buildSlidePayload(array $data): ?array
    {
        if (empty($data['id'])) {
            return null;
        }

        $payload = [
            'id' => $data['id'],
            'productId' => $data['productId'] ?? null,
            'categoryId' => $data['categoryId'] ?? null,
            'slideCoverId' => $data['slideCoverId'] ?? null,
            'slideCoverMobileId' => $data['slideCoverMobileId'] ?? null,
            'slideCoverTabletId' => $data['slideCoverTabletId'] ?? null,
            'slideCoverVideoId' => $data['slideCoverVideoId'] ?? null,
            'presentationMediaId' => $data['presentationMediaId'] ?? null,
            'activeFrom' => $data['activeFrom'] ?? null,
            'activeUntil' => $data['activeUntil'] ?? null,
            'slideSettings' => $data['slideSettings'] ?? null,
        ];

        if (!empty($data['translations']) && is_array($data['translations'])) {
            $payload['translations'] = [];

            foreach ($data['translations'] as $languageId => $translation) {
                $payload['translations'][] = [
                    'languageId' => $languageId,
                    'name' => $translation['name'] ?? null,
                    'title' => $translation['title'] ?? null,
                    'description' => $translation['description'] ?? null,
                    'buttonLabel' => $translation['buttonLabel'] ?? null,
                    'url' => $translation['url'] ?? null,
                    'customFields' => $translation['customFields'] ?? null,
                    'contentSettings' => $translation['contentSettings'] ?? null,
                ];
            }
        }

        // Check if at least one translation has a name
        if (isset($payload['translations']) && !empty($payload['translations'])) {
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
        }

        return $payload;
    }
}
