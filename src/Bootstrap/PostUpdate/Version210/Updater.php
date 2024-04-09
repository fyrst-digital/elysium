<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Bootstrap\PostUpdate\Version210;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Administration\Notification\NotificationService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Blur\BlurElysiumSlider\Bootstrap\PostUpdate\Version210\SlideSettings;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class Updater
{
    function __construct(
        private readonly Connection $connection,
        private readonly Context $context,
        private readonly EntityRepository $slidesRepository,
        private readonly EntityRepository $cmsSlotRepository,
        private readonly NotificationService $notificationService
    ) {
    }

    public function run(): void
    {
        $this->convertSlideSettings();
        $this->convertCmsSliderConfig();
        $this->cmsSlotRemoveDeprecatedConfig();
    }

    private function cmsSlotRemoveDeprecatedConfig(): void
    {

        $this->connection->executeStatement(
            "UPDATE `cms_slot_translation` 
            LEFT JOIN `cms_slot` ON `cms_slot_translation`.`cms_slot_id` = `cms_slot`.`id`
            SET `config` = JSON_REMOVE(
            `config`, 
            '$.sizing',
            '$.aspectRatio',
            '$.slideSpeed',
            '$.sliderOverlay',
            '$.sliderAutoplay',
            '$.sliderDotColor',
            '$.sliderArrowColor',
            '$.sliderNavigation',
            '$.sliderDotActiveColor',
            '$.sliderAutoplayTimeout'
            )
            WHERE `cms_slot`.`type` = :element",
            [
                'element' => 'blur-elysium-slider',
            ]
        );
    }

    private function convertSlideSettings(): void
    {
        $defaultSlideSettings = (new SlideSettings())->getSettings();
        $criteria = new Criteria();
        $result = $this->slidesRepository->search($criteria, $this->context);
        $updateSlideSettings = [];

        if ($result->getTotal() > 0) {

            foreach ($result->getElements() as $id => $slide) {
                $slideSettings = $slide->get('slideSettings');
                $convertedSlideSettings = [];
                $convertedSlideSettings['id'] = $id;
                $convertedSlideSettings['slideSettings'] = \array_replace_recursive($defaultSlideSettings, $slideSettings);

                /**
                 * convert slide settings
                 */
                # slide headline 
                $convertedSlideSettings['slideSettings']['slide']['headline']['element'] = isset($slideSettings['headlineElement']) && !empty($slideSettings['headlineElement']) ? $slideSettings['headlineElement'] : 'div';
                $convertedSlideSettings['slideSettings']['slide']['headline']['color'] = isset($slideSettings['headlineTextcolor']) && !empty($slideSettings['headlineTextcolor']) ? $slideSettings['headlineTextcolor'] : null;
                # slide general
                $convertedSlideSettings['slideSettings']['slide']['bgColor'] = isset($slideSettings['slideBgColor']) && !empty($slideSettings['slideBgColor']) ? $slideSettings['slideBgColor'] : null;
                $convertedSlideSettings['slideSettings']['slide']['cssClass'] = isset($slideSettings['slideCssClass']) && !empty($slideSettings['slideCssClass']) ? $slideSettings['slideCssClass'] : null;
                # slide linking
                $convertedSlideSettings['slideSettings']['slide']['linking']['overlay'] = isset($slideSettings['urlOverlay']) && !empty($slideSettings['urlOverlay']) ? $slideSettings['urlOverlay'] : false;
                $convertedSlideSettings['slideSettings']['slide']['linking']['openExternal'] = isset($slideSettings['urlTarget']) && $slideSettings['urlTarget'] === 'external' ? true : false;
                $convertedSlideSettings['slideSettings']['slide']['linking']['buttonAppearance'] = isset($slideSettings['buttonAppearance']) && !empty($slideSettings['buttonAppearance']) ? $slideSettings['buttonAppearance'] : 'primary';
                # container
                $convertedSlideSettings['slideSettings']['container']['bgColor'] = isset($slideSettings['containerBgColor']) && !empty($slideSettings['containerBgColor']) ? $slideSettings['containerBgColor'] : null;
                # slide template
                $convertedSlideSettings['slideSettings']['customTemplateFile'] = isset($slideSettings['customTemplateFile']) && !empty($slideSettings['customTemplateFile']) ? $slideSettings['customTemplateFile'] : null;

                # container padding
                if (isset($slideSettings['containerPadding']) && !empty($slideSettings['containerPadding'])) {
                    $containerPaddingInt = $this->convertStringToIntUnit($slideSettings['containerPadding']);

                    if (!empty($containerPaddingInt)) {
                        # viewport mobile
                        $convertedSlideSettings['slideSettings']['viewports']['mobile']['container']['paddingX'] = $containerPaddingInt;
                        $convertedSlideSettings['slideSettings']['viewports']['mobile']['container']['paddingY'] = $containerPaddingInt;
                        # viewport tablet
                        $convertedSlideSettings['slideSettings']['viewports']['tablet']['container']['paddingX'] = $containerPaddingInt;
                        $convertedSlideSettings['slideSettings']['viewports']['tablet']['container']['paddingY'] = $containerPaddingInt;
                        # viewport desktop
                        $convertedSlideSettings['slideSettings']['viewports']['desktop']['container']['paddingX'] = $containerPaddingInt;
                        $convertedSlideSettings['slideSettings']['viewports']['desktop']['container']['paddingY'] = $containerPaddingInt;
                    }
                }

                # container max width
                if (isset($slideSettings['containerMaxWidth']) && !empty($slideSettings['containerMaxWidth'])) {
                    $containerMaxWidthInt = $this->convertStringToIntUnit($slideSettings['containerMaxWidth']);

                    if (!empty($containerMaxWidthInt)) {
                        # viewport mobile
                        $convertedSlideSettings['slideSettings']['viewports']['mobile']['container']['maxWidth'] = $containerMaxWidthInt;
                        $convertedSlideSettings['slideSettings']['viewports']['mobile']['container']['maxWidthDisabled'] = false;
                        # viewport tablet
                        $convertedSlideSettings['slideSettings']['viewports']['tablet']['container']['maxWidth'] = $containerMaxWidthInt;
                        $convertedSlideSettings['slideSettings']['viewports']['tablet']['container']['maxWidthDisabled'] = false;
                        # viewport desktop
                        $convertedSlideSettings['slideSettings']['viewports']['desktop']['container']['maxWidth'] = $containerMaxWidthInt;
                        $convertedSlideSettings['slideSettings']['viewports']['desktop']['container']['maxWidthDisabled'] = false;
                    }
                }

                # container vertical align
                if (isset($slideSettings['containerVerticalAlign']) && !empty($slideSettings['containerVerticalAlign'])) {
                    $containerAlignItems = null;

                    if ($slideSettings['containerVerticalAlign'] === 'center') {
                        $containerAlignItems = 'center';
                    } elseif ($slideSettings['containerVerticalAlign'] === 'top') {
                        $containerAlignItems = 'flex-start';
                    } elseif ($slideSettings['containerVerticalAlign'] === 'bottom') {
                        $containerAlignItems = 'flex-end';
                    }

                    if ($containerAlignItems !== null) {
                        $convertedSlideSettings['slideSettings']['viewports']['mobile']['slide']['alignItems'] = $containerAlignItems;
                        $convertedSlideSettings['slideSettings']['viewports']['tablet']['slide']['alignItems'] = $containerAlignItems;
                        $convertedSlideSettings['slideSettings']['viewports']['desktop']['slide']['alignItems'] = $containerAlignItems;
                    }
                }

                # container horizontal align
                if (isset($slideSettings['containerHorizontalAlign']) && !empty($slideSettings['containerHorizontalAlign'])) {
                    $containerJustifyContent = null;

                    if ($slideSettings['containerHorizontalAlign'] === 'center') {
                        $containerJustifyContent = 'center';
                    } elseif ($slideSettings['containerHorizontalAlign'] === 'left') {
                        $containerJustifyContent = 'flex-start';
                    } elseif ($slideSettings['containerHorizontalAlign'] === 'right') {
                        $containerJustifyContent = 'flex-end';
                    }

                    if ($containerJustifyContent !== null) {
                        $convertedSlideSettings['slideSettings']['viewports']['mobile']['slide']['justifyContent'] = $containerJustifyContent;
                        $convertedSlideSettings['slideSettings']['viewports']['tablet']['slide']['justifyContent'] = $containerJustifyContent;
                        $convertedSlideSettings['slideSettings']['viewports']['desktop']['slide']['justifyContent'] = $containerJustifyContent;
                    }
                }

                # content vertical align
                if (isset($slideSettings['contentVerticalAlign']) && !empty($slideSettings['contentVerticalAlign'])) {
                    $contentTextAlign = null;

                    if ($slideSettings['contentVerticalAlign'] === 'center') {
                        $contentTextAlign = 'center';
                    } elseif ($slideSettings['contentVerticalAlign'] === 'left') {
                        $contentTextAlign = 'left';
                    } elseif ($slideSettings['contentVerticalAlign'] === 'right') {
                        $contentTextAlign = 'right';
                    }

                    if ($contentTextAlign !== null) {
                        $convertedSlideSettings['slideSettings']['viewports']['mobile']['content']['textAlign'] = $contentTextAlign;
                        $convertedSlideSettings['slideSettings']['viewports']['tablet']['content']['textAlign'] = $contentTextAlign;
                        $convertedSlideSettings['slideSettings']['viewports']['desktop']['content']['textAlign'] = $contentTextAlign;
                    }
                }

                $updateSlideSettings[] = $convertedSlideSettings;
            }

            /**
             * @todo make message translation aware
             */
            try {
                $this->slidesRepository->update($updateSlideSettings, $this->context);
            } catch (\Exception $e) {
                $this->notificationService->createNotification(
                    [
                        'status' => 'error',
                        'message' => 'Something went wrong during the Elysium Slide settings conversion'
                    ],
                    $this->context
                );
            }
        }
    }

    private function convertCmsSliderConfig(): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('type', 'blur-elysium-slider'));
        $result = $this->cmsSlotRepository->search($criteria, $this->context);
        $updatedCmsElementsConfig = [];

        if ($result->getTotal() > 0) {

            foreach ($result->getElements() as $id => $cmsElement) {
                /** @var CmsSlotEntity $cmsElement */
                $cmsElementConfig = $cmsElement->getConfig();
                $convertedCmsElementConfig = [];
                $convertedCmsElementConfig['id'] = $id;
                $convertedCmsElementConfig['config'] = $cmsElementConfig;
                $convertedCmsElementConfig['config']['viewports']['source'] = 'static';

                $viewportConfig = [
                    'mobile' => [
                        'aspectRatio' => isset($cmsElementConfig['aspectRatio']['value']) ? $this->getPropertyFromViewportArray('xs', $cmsElementConfig['aspectRatio']['value']) : null,
                        'sizing' => isset($cmsElementConfig['sizing']['value']) ? $this->getPropertyFromViewportArray('xs', $cmsElementConfig['sizing']['value'], ['paddingY' => 40, 'paddingX' => 40]) : null,
                        'arrows' => [
                            'iconSize' => 16
                        ]
                    ],
                    'tablet' => [
                        'aspectRatio' => isset($cmsElementConfig['aspectRatio']['value']) ? $this->getPropertyFromViewportArray('md', $cmsElementConfig['aspectRatio']['value']) : null,
                        'sizing' => isset($cmsElementConfig['sizing']['value']) ? $this->getPropertyFromViewportArray('md', $cmsElementConfig['sizing']['value'], ['paddingY' => 40, 'paddingX' => 40]) : null,
                        'arrows' => [
                            'iconSize' => 20
                        ]
                    ],
                    'desktop' => [
                        'aspectRatio' => isset($cmsElementConfig['aspectRatio']['value']) ? $this->getPropertyFromViewportArray('xxl', $cmsElementConfig['aspectRatio']['value']) : null,
                        'sizing' => isset($cmsElementConfig['sizing']['value']) ? $this->getPropertyFromViewportArray('xxl', $cmsElementConfig['sizing']['value'], ['paddingY' => 40, 'paddingX' => 40]) : null,
                        'arrows' => [
                            'iconSize' => 24
                        ]
                    ]
                ];

                foreach ($viewportConfig as $viewport => $config) {
                    $convertedCmsElementConfig['config']['viewports']['value'][$viewport]['sizing']['aspectRatio'] = isset($config['aspectRatio']['aspectRatio']) && !empty($config['aspectRatio']['aspectRatio']) ? $config['aspectRatio']['aspectRatio'] : null;
                    $convertedCmsElementConfig['config']['viewports']['value'][$viewport]['sizing']['maxHeight'] = isset($config['sizing']['maxHeight']) && !empty($config['sizing']['maxHeight']) ? $this->convertStringToIntUnit($config['sizing']['maxHeight']) : null;
                    $convertedCmsElementConfig['config']['viewports']['value'][$viewport]['settings']['slidesPerPage'] = isset($config['sizing']['slidesPerPage']) && !empty($config['sizing']['slidesPerPage']) ? $config['sizing']['slidesPerPage'] : 1;
                    $convertedCmsElementConfig['config']['viewports']['value'][$viewport]['arrows']['iconSize'] = $config['arrows']['iconSize'];
                }

                # slide speed
                $convertedCmsElementConfig['config']['settings']['value']['speed'] = isset($cmsElementConfig['slideSpeed']['value']) ? $cmsElementConfig['slideSpeed']['value'] : null;

                $updatedCmsElementsConfig[] = $convertedCmsElementConfig;
            }

            /**
             * @todo make message translation aware
             */
            try {
                $this->cmsSlotRepository->update($updatedCmsElementsConfig, $this->context);
            } catch (\Exception $e) {
                $this->notificationService->createNotification(
                    [
                        'status' => 'error',
                        'message' => 'Something went wrong during the Elysium Slider config conversion'
                    ],
                    $this->context
                );
                throw $e;
            }
        }
    }

    /**
     * @param mixed[]|null $config
     * @return mixed
     */
    private function getPropertyFromViewportArray(string $viewport, ?array $config, ?array $enrich = null, ?string $property = null): mixed
    {
        if ($config === null) {
            return null;
        }

        $result = array_merge(...\array_filter($config, function ($value) use ($viewport) {
            return $value['viewport'] === $viewport;
        }));

        if ($enrich !== null) {
            $result = \array_replace_recursive($result, $enrich);
        }

        return $result;
    }

    function convertStringToIntUnit(string $value): ?int
    {
        /**
         * Function is aware of rem unit. If rem is provided in $value string, null will be returned
         */
        $intValue = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);

        if ($intValue !== false && \preg_match('/rem/', $value) === 1) {
            return $intValue * 16;
        }

        return $intValue;
    }
}
