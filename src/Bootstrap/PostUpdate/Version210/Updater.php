<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Bootstrap\PostUpdate\Version210;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Administration\Notification\NotificationService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Blur\BlurElysiumSlider\Bootstrap\PostUpdate\Version210\SlideSettings;

class Updater
{
    function __construct(
        private readonly Connection $connection,
        private readonly Context $context,
        private readonly EntityRepository $slidesRepository,
        private readonly NotificationService $notificationService
    ) {
    }

    public function run(): void
    {
        $this->cmsSlotRemoveDeprecatedConfig();
        $this->convertSlideSettings();
    }

    private function cmsSlotRemoveDeprecatedConfig(): void
    {

        $this->connection->executeStatement(
            "UPDATE `cms_slot_translation` 
            LEFT JOIN `cms_slot` ON `cms_slot_translation`.`cms_slot_id` = `cms_slot`.`id`
            SET `config` = JSON_REMOVE(
            `config`, 
            '$.sizing'
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
                $convertedSlideSettings['slideSettings'] = $defaultSlideSettings;

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
                    $containerPaddingInt = (int) filter_var($slideSettings['containerPadding'], FILTER_SANITIZE_NUMBER_INT);

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
                    $containerMaxWidthInt = (int) filter_var($slideSettings['containerMaxWidth'], FILTER_SANITIZE_NUMBER_INT);

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
}
