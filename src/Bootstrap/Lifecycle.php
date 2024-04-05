<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Bootstrap;

use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Content\Media\Aggregate\MediaDefaultFolder\MediaDefaultFolderEntity;
use Shopware\Core\Content\Media\Aggregate\MediaFolder\MediaFolderEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Administration\Notification\NotificationService;
use Blur\BlurElysiumSlider\Bootstrap\UpdateMigration\Update210CmsSlotSliderRemoveSizing;

class Lifecycle
{
    /** @var string */
    private const MEDIA_FOLDER_NAME = 'Elysium Slides';

    private string $mediaFolderId;

    private string $mediaDefaultFolderId;

    private ?string $mediaFolderConfigurationId;

    private NotificationService $notificationService;

    public function __construct(
        private readonly ContainerInterface $container
    ) {
        $this->notificationService = $container->get(NotificationService::class);
    }

    public function install(Context $context): void
    {
        # create IDs
        $this->setMediaFolderId(Uuid::randomHex());
        $this->setMediaDefaultFolderId(Uuid::randomHex());

        # create media default folder entry
        $this->createMediaDefaultFolder($context);

        # create media folder entry 
        $this->createMediaFolder($context);
    }

    public function postUpdate(UpdateContext $updateContext): void
    {
        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);

        if (\version_compare($updateContext->getCurrentPluginVersion(), '2.1.0', '<')) {
            (new Update210CmsSlotSliderRemoveSizing($connection))->execute();
            $this->convertSlideSettings210($updateContext->getContext());

            /**
             * example for feature data conversion of repositories on post update
             * $this->postUpdate210($updateContext->getContext());
             */
        }
    }

    public function uninstall(Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('entity', 'blur_elysium_slides'));
        $criteria->addAssociation('media_folder');
        $criteria->setLimit(1);

        /** @var EntityRepository $mediaFolderRepositroy */
        $mediaFolderRepositroy = $this->container->get('media_folder.repository');

        /** @var EntityRepository */
        $mediaDefaultFolderRepositroy = $this->container->get('media_default_folder.repository');

        /** @var EntityRepository */
        $mediaFolderConfigurationRepositroy = $this->container->get('media_folder_configuration.repository');

        /** @var MediaDefaultFolderEntity $mediaFolderElysiumSlides */
        $mediaFolderElysiumSlides = $mediaDefaultFolderRepositroy->search($criteria, $context)->first();


        if ($mediaFolderElysiumSlides->getId()) {
            $this->setMediaDefaultFolderId($mediaFolderElysiumSlides->getId());
        }

        # existence check
        if ($mediaFolderElysiumSlides->getFolder() !== null) {
            /** @var MediaFolderEntity $elysiumSlidesFolder */
            $elysiumSlidesFolder = $mediaFolderElysiumSlides->getFolder();
            $this->setMediaFolderId(
                $elysiumSlidesFolder->getId()
            );
            $this->setMediaFolderConfigurationId(
                $elysiumSlidesFolder->getConfigurationId()
            );
        }

        if (!empty($this->getMediaFolderId())) {
            $mediaFolderRepositroy->delete([['id' => $this->getMediaFolderId()]], $context);
        }

        if (!empty($this->getMediaDefaultFolderId())) {
            $mediaDefaultFolderRepositroy->delete([['id' => $this->getMediaDefaultFolderId()]], $context);
        }

        if (!empty($this->getMediaFolderConfigurationId())) {
            # delete media folder configuration entry
            $mediaFolderConfigurationRepositroy->delete([['id' => $this->getMediaFolderConfigurationId()]], $context);
        }
    }

    private function createMediaDefaultFolder(
        Context $context
    ): void {

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('entity', 'blur_elysium_slides'));
        $criteria->addAssociation('folder');
        $criteria->setLimit(1);

        /** @var EntityRepository */
        $mediaDefaultFolderRepositroy = $this->container->get('media_default_folder.repository');

        /** @var EntitySearchResult */
        $mediaDefaultFolderResult = $mediaDefaultFolderRepositroy->search($criteria, $context);

        if ($mediaDefaultFolderResult->getTotal() <= 0) {
            # create new media default for blur_elysium_slides
            $mediaDefaultFolderRepositroy->create([
                [
                    'id' => $this->getMediaDefaultFolderId(),
                    'associationFields' => ["media", "mediaPortrait"],
                    'entity' => 'blur_elysium_slides'
                ]
            ], $context);
        } else {
            # if there is already a default folder for blur_elysium_slides
            # check possible associations to an existing media folder linked to it
            # if there is an existing media folder association set this as mediaFolderId for security check purpose
            /** @var MediaDefaultFolderEntity $mediaDefaultFolders */
            $mediaDefaultFolders = $mediaDefaultFolderResult->first();
            /** @var MediaFolderEntity $elysiumMediaFolder */
            $elysiumMediaFolder = $mediaDefaultFolders->getFolder();
            $this->setMediaFolderId($elysiumMediaFolder->getId());
        }
    }

    private function createMediaFolder(
        Context $context
    ): void {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $this->getMediaFolderId()));
        /** @var EntityRepository $mediaFolderRepositroy */
        $mediaFolderRepositroy = $this->container->get('media_folder.repository');

        if ($mediaFolderRepositroy->search($criteria, $context)->getTotal() <= 0) {
            $mediaFolderRepositroy->create([
                [
                    'id' => $this->getMediaFolderId(),
                    'name' => self::MEDIA_FOLDER_NAME,
                    'useParentConfiguration' => false,
                    'configuration' => [
                        /**
                     * @TODO
                     * discard the idea of setting custom thumbnails because of buggy behavior of shopware
                     * review the possibility of custom thumbnails later on
                     */
                        //'mediaThumbnailSizes' => self::MEDIA_THUMBNAIL_SIZES
                    ],
                    'defaultFolderId' => $this->getMediaDefaultFolderId()
                ]
            ], $context);
        }
    }

    private function convertSlideSettings210(
        Context $context
    ): void {
        $criteria = new Criteria();
        /* EntityRepository $cmsSlotRepository */
        $repository = $this->container->get('blur_elysium_slides.repository');
        $result = $repository->search($criteria, $context);
        $updateSlideSettings = [];

        if ($result->getTotal() > 0) {

            foreach ($result->getElements() as $id => $slide) {
                $slideSettings = $slide->get('slideSettings');
                $convertedSlideSettings = [];
                $convertedSlideSettings['id'] = $id;

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
                        # viewport tablet
                        $convertedSlideSettings['slideSettings']['viewports']['tablet']['container']['maxWidth'] = $containerMaxWidthInt;
                        # viewport desktop
                        $convertedSlideSettings['slideSettings']['viewports']['desktop']['container']['maxWidth'] = $containerMaxWidthInt;
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
                $repository->update($updateSlideSettings, $context);
            } catch (\Exception $e) {
                $this->notificationService->createNotification(
                    [
                        'status' => 'error',
                        'message' => 'Something went wrong during the Elysium Slide settings conversion'
                    ],
                    $context
                );
            }
        }
    }

    /**
     * Get the value of mediaFolderId
     * @return string
     */
    private function getMediaFolderId(): string
    {
        return $this->mediaFolderId;
    }

    /**
     * Set the value of mediaFolderId
     *
     * @return void
     */
    private function setMediaFolderId(string $mediaFolderId): void
    {
        $this->mediaFolderId = $mediaFolderId;
    }

    /**
     * Get the value of mediaDefaultFolderId
     */
    private function getMediaDefaultFolderId(): string
    {
        return $this->mediaDefaultFolderId;
    }

    /**
     * Set the value of mediaDefaultFolderId
     *
     * @return void
     */
    private function setMediaDefaultFolderId(string $mediaDefaultFolderId): void
    {
        $this->mediaDefaultFolderId = $mediaDefaultFolderId;
    }

    /**
     * Get the value of mediaFolderConfigurationId
     */
    private function getMediaFolderConfigurationId(): ?string
    {
        return $this->mediaFolderConfigurationId;
    }

    /**
     * Set the value of mediaFolderConfigurationId
     *
     * @param string|null $mediaFolderConfigurationId
     * @return void
     */
    private function setMediaFolderConfigurationId(?string $mediaFolderConfigurationId): void
    {
        $this->mediaFolderConfigurationId = $mediaFolderConfigurationId;
    }
}
