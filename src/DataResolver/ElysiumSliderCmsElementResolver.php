<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\DataResolver;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\SalesChannel\ElysiumSlideLoader;
use Blur\BlurElysiumSlider\Struct\ElysiumSliderStruct;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events\ElysiumCmsSlidesCriteriaEvent;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfig;
use Shopware\Core\Content\Cms\DataResolver\FieldConfigCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ElysiumSliderCmsElementResolver extends AbstractCmsElementResolver
{
    /**
     * Single source of truth for slider viewport options.
     *
     * Adding a new option only requires adding one entry here.
     */
    private const SLIDER_VIEWPORT_CONFIG = [
        'slidesPerView'  => ['group' => 'settings', 'key' => 'slidesPerPage',  'default' => 1],
        'slidesPerGroup' => ['group' => 'settings', 'key' => 'slidesPerGroup', 'default' => 1],
        'spaceBetween'   => ['group' => 'sizing',   'key' => 'slidesGap',      'default' => 0],
    ];

    public function __construct(
        private readonly ElysiumSlideLoader $slideLoader,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    public function getType(): string
    {
        return 'blur-elysium-slider';
    }

    public function collect(
        CmsSlotEntity $slot,
        ResolverContext $resolverContext
    ): ?CriteriaCollection {
        return null;
    }

    public function enrich(
        CmsSlotEntity $slot,
        ResolverContext $resolverContext,
        ElementDataCollection $result
    ): void {
        $context = $resolverContext->getSalesChannelContext();
        /** @var ElysiumSliderStruct $elysiumSliderStruct */
        $elysiumSliderStruct = new ElysiumSliderStruct();
        /** @var FieldConfigCollection $fieldConfigCollection */
        $fieldConfigCollection = $slot->getFieldConfig();
        /** @var FieldConfig $elysiumSlideConfig */
        $elysiumSlideConfig = $fieldConfigCollection->get('elysiumSlideCollection');
        /** @var string[] $elysiumSlideIds */
        $elysiumSlideIds = $elysiumSlideConfig->getValue();

        $criteria = new Criteria();

        $this->eventDispatcher->dispatch(
            new ElysiumCmsSlidesCriteriaEvent($criteria, $context, $slot)
        );

        $elysiumSliderStruct->setResolvedViewports(
            $this->resolveViewports($fieldConfigCollection)
        );

        if (!empty($elysiumSlideIds)) {
            $slides = $this->slideLoader->load($elysiumSlideIds, $criteria, $context, "cms-element-elysium-slider-{$slot->getId()}");

            $elysiumSliderStruct->setSlideCollection($slides->getElements());
        }

        $slot->setData($elysiumSliderStruct);
    }

    /**
     * Resolve viewport settings with mobile-first inheritance.
     * Tablet falls back to mobile, desktop falls back to tablet (then mobile).
     */
    private function resolveViewports(FieldConfigCollection $fieldConfigCollection): array
    {
        /** @var FieldConfig $viewportsConfig */
        $viewportsConfig = $fieldConfigCollection->get('viewports');
        /** @var array<string, array<string, mixed>> $viewports */
        $viewports = $viewportsConfig->getValue() ?? [];

        $defaults = $this->buildDefaultsFromConfig();

        $mobile = $this->deepMerge($defaults, $viewports['mobile'] ?? []);
        $tablet = $this->deepMerge($mobile, $viewports['tablet'] ?? []);
        $desktop = $this->deepMerge($tablet, $viewports['desktop'] ?? []);

        return [
            'mobile' => $this->extractViewportOptions($mobile),
            'tablet' => $this->extractViewportOptions($tablet),
            'desktop' => $this->extractViewportOptions($desktop),
        ];
    }

    /**
     * Build the nested defaults array from SLIDER_VIEWPORT_CONFIG.
     */
    private function buildDefaultsFromConfig(): array
    {
        $defaults = [];

        foreach (self::SLIDER_VIEWPORT_CONFIG as $config) {
            $group = $config['group'];
            $key = $config['key'];
            $default = $config['default'];

            if (!isset($defaults[$group])) {
                $defaults[$group] = [];
            }

            $defaults[$group][$key] = $default;
        }

        return $defaults;
    }

    /**
     * Extract slider viewport options from a resolved viewport config.
     */
    private function extractViewportOptions(array $viewport): array
    {
        $options = [];

        foreach (self::SLIDER_VIEWPORT_CONFIG as $swiperKey => $config) {
            $group = $config['group'];
            $key = $config['key'];
            $options[$swiperKey] = $viewport[$group][$key];
        }

        return $options;
    }

    /**
     * Deep-merge $override into $base. Null values in $override are skipped
     * so they do not overwrite inherited values (mobile-first behavior).
     */
    private function deepMerge(array $base, array $override): array
    {
        $result = $base;

        foreach ($override as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_array($value) && isset($result[$key]) && is_array($result[$key])) {
                $result[$key] = $this->deepMerge($result[$key], $value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
