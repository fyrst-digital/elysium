<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Service\ImportExport;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;

class SlideExportService
{
    public function __construct(
        private readonly EntityRepository $slideRepository
    ) {
    }

    public function export(array $slideIds, Context $context): string
    {
        if (empty($slideIds)) {
            return $this->createHeaderLine(0);
        }

        $criteria = new Criteria($slideIds);
        $criteria->addAssociation('translations');

        $slides = $this->slideRepository->search($criteria, $context);

        $lines = [$this->createHeaderLine($slides->count())];

        /** @var ElysiumSlidesEntity $slide */
        foreach ($slides as $slide) {
            $lines[] = $this->encodeSlide($slide);
        }

        return implode("\n", $lines);
    }

    public function exportAll(Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addAssociation('translations');

        $slides = $this->slideRepository->search($criteria, $context);

        $lines = [$this->createHeaderLine($slides->count())];

        /** @var ElysiumSlidesEntity $slide */
        foreach ($slides as $slide) {
            $lines[] = $this->encodeSlide($slide);
        }

        return implode("\n", $lines);
    }

    private function createHeaderLine(int $count): string
    {
        return json_encode([
            'type' => 'elysium-slides-export',
            'version' => '1.0',
            'exportedAt' => (new \DateTime())->format('c'),
            'count' => $count,
        ], JSON_THROW_ON_ERROR);
    }

    private function encodeSlide(ElysiumSlidesEntity $slide): string
    {
        $data = [
            'id' => $slide->getId(),
            'productId' => $slide->getProductId(),
            'categoryId' => $slide->getCategoryId(),
            'slideCoverId' => $slide->getSlideCoverId(),
            'slideCoverMobileId' => $slide->getSlideCoverMobileId(),
            'slideCoverTabletId' => $slide->getSlideCoverTabletId(),
            'slideCoverVideoId' => $slide->getSlideCoverVideoId(),
            'presentationMediaId' => $slide->getPresentationMediaId(),
            'activeFrom' => $slide->getActiveFrom() ? $slide->getActiveFrom()->format('c') : null,
            'activeUntil' => $slide->getActiveUntil() ? $slide->getActiveUntil()->format('c') : null,
            'slideSettings' => $slide->getSlideSettings(),
            'translations' => $this->encodeTranslations($slide),
        ];

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    private function encodeTranslations(ElysiumSlidesEntity $slide): array
    {
        $translations = [];

        foreach ($slide->getTranslations() ?? [] as $translation) {
            $translations[$translation->getLanguageId()] = [
                'name' => $translation->getName(),
                'title' => $translation->getTitle(),
                'description' => $translation->getDescription(),
                'buttonLabel' => $translation->getButtonLabel(),
                'url' => $translation->getUrl(),
                'customFields' => $translation->getCustomFields(),
                'contentSettings' => $translation->getContentSettings(),
            ];
        }

        return $translations;
    }
}
