<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Service;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation\ElysiumSlidesTranslationEntity;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Blur\BlurElysiumSlider\Framework\DataAbstractionLayer\EntitySearch;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class SlideCoverImageSwitchService
{
    private const BATCH_SIZE = 100;

    public function __construct(
        private readonly EntityRepository $slideRepository
    ) {
    }

    public function switch(Context $context): int
    {
        $offset = 0;
        $payload = [];

        do {
            $criteria = new Criteria();
            $criteria->addAssociation('translations');
            $criteria->setLimit(self::BATCH_SIZE);
            $criteria->setOffset($offset);

            $result = EntitySearch::fetch($this->slideRepository, $criteria, $context);

            /** @var ElysiumSlidesEntity $slide */
            foreach ($result['entities'] as $slide) {
                $slidePayload = $this->buildSlidePayload($slide);
                if ($slidePayload !== null) {
                    $payload[] = $slidePayload;
                }
            }

            $pageCount = $result['entities']->count();
            $offset += self::BATCH_SIZE;
        } while ($pageCount === self::BATCH_SIZE);

        if ($payload === []) {
            return 0;
        }

        $this->slideRepository->upsert($payload, $context);

        return \count($payload);
    }

    /**
     * @return array{id: string, translations: list<array{languageId: string, contentSettings: array<string, mixed>}>}|null
     */
    private function buildSlidePayload(ElysiumSlidesEntity $slide): ?array
    {
        $translationsPayload = [];

        foreach ($slide->getTranslations() ?? [] as $translation) {
            $updatedSettings = $this->switchTranslationCover($translation);
            if ($updatedSettings === null) {
                continue;
            }

            $translationsPayload[] = [
                'languageId' => $translation->getLanguageId(),
                'contentSettings' => $updatedSettings,
            ];
        }

        if ($translationsPayload === []) {
            return null;
        }

        return [
            'id' => $slide->getId(),
            'translations' => $translationsPayload,
        ];
    }

    /**
     * Moves desktop cover to mobile when mobile is empty. Reads the translation
     * row directly so CLI hydration fallbacks are not persisted.
     *
     * @return array<string, mixed>|null
     */
    private function switchTranslationCover(ElysiumSlidesTranslationEntity $translation): ?array
    {
        $contentSettings = $translation->getContentSettings() ?? [];
        $slideCover = $contentSettings['slideCover'] ?? [];

        if (!\is_array($slideCover)) {
            return null;
        }

        $mobileId = $slideCover['mobileId'] ?? null;
        $desktopId = $slideCover['desktopId'] ?? null;

        if (!$this->isEmptyMediaId($mobileId) || $this->isEmptyMediaId($desktopId)) {
            return null;
        }

        $contentSettings['slideCover'] = array_merge($slideCover, [
            'mobileId' => $desktopId,
            'desktopId' => null,
        ]);

        return $contentSettings;
    }

    private function isEmptyMediaId(mixed $value): bool
    {
        return $value === null || $value === '';
    }
}
