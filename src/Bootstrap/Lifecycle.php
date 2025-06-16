<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Bootstrap;

use Blur\BlurElysiumSlider\Bootstrap\PostUpdate\Version210\Updater as Version210Updater;
use Blur\BlurElysiumSlider\Defaults;
use Doctrine\DBAL\Connection;
use Shopware\Administration\Notification\NotificationService;
use Shopware\Core\Content\Media\Aggregate\MediaFolder\MediaFolderCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Migration\Traits\EnsureThumbnailSizesTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Lifecycle
{
    use EnsureThumbnailSizesTrait;

    private NotificationService $notificationService;

    public function __construct(
        private readonly ContainerInterface $container
    ) {
        /** @phpstan-ignore-next-line */
        $this->notificationService = $container->get(NotificationService::class);
    }

    public function postInstall(InstallContext $installContext): void
    {
        /** @var Context $context */
        $context = $installContext->getContext();
        $this->createMediaFolder($context);
    }

    public function postUpdate(UpdateContext $updateContext): void
    {
        /**
         * @var array<mixed>
         */
        $postUpdater = [];

        if (\version_compare($updateContext->getCurrentPluginVersion(), $version = '2.0.0', '<')) {
            $postUpdater[$version] = new Version210Updater(
                /** @phpstan-ignore-next-line */
                $this->container->get(Connection::class),
                $updateContext->getContext(),
                /** @phpstan-ignore-next-line */
                $this->container->get('blur_elysium_slides.repository'),
                /** @phpstan-ignore-next-line */
                $this->container->get('cms_slot.repository'),
                $this->notificationService
            );
        }

        if (\count($postUpdater) > 0) {
            foreach ($postUpdater as $key => $update) {
                $update->run();
            }
        }
    }

    public function uninstall(Context $context): void
    {
        $this->removeDatabaseTables();
    }

    public function getMediaThumbnailSizesIds(): ?array
    {
        $thumbnailSizeIds = null;

        $thumbnailSizeIds = $this->ensureThumbnailSizes(Defaults::MEDIA_THUMBNAIL_SIZES, $this->container->get(Connection::class));

        if (\count($thumbnailSizeIds) > 0) {
            $thumbnailSizeIds = array_map(function ($thumbnailSize) {
                return ['id' => bin2hex($thumbnailSize)];
            }, $thumbnailSizeIds);
        }

        return $thumbnailSizeIds;
    }

    private function removeDatabaseTables(): void
    {
        $connection = $this->container->get(Connection::class);

        /**
         * Delete media folder with dependencies
         */
        try {
            $connection->executeStatement('
                DELETE mf, mdf, mfc
                FROM media_folder mf
                JOIN media_default_folder mdf ON mf.default_folder_id = mdf.id
                JOIN media_folder_configuration mfc ON mf.media_folder_configuration_id = mfc.id
                WHERE mf.id = UNHEX(:mediaFolderId)
            ', [
                'mediaFolderId' => Defaults::MEDIA_FOLDER_ID,
            ]);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to remove tables and data: ' . $e->getMessage());
        }

        /**
         * Delete elysium slides tables
         */
        try {
            $connection->executeStatement('DROP TABLE IF EXISTS `blur_elysium_slides_translation`, `blur_elysium_slides`');
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to drop tables: ' . $e->getMessage());
        }
    }

    private function createMediaFolder(
        Context $context
    ): void {
        /** @var EntityRepository<MediaFolderCollection> $mediaFolderRepositroy */
        $mediaFolderRepositroy = $this->container->get('media_folder.repository');

        $searchCriteria = new Criteria([Defaults::MEDIA_FOLDER_ID]);
        $searchCriteria->addAssociation('configuration');
        $searchCriteria->addAssociation('defaultFolder');
        $searchCriteria->setLimit(1);

        /** @var EntitySearchResult $searchMediaFolder */
        $searchMediaFolder = $mediaFolderRepositroy->search($searchCriteria, $context);
        /** @var array<'id', string>|null $thumbnailSizeIds */
        $thumbnailSizeIds = $this->getMediaThumbnailSizesIds();

        /**
         * If there is no existing slides media folder
         * create it with default media thumbnails
         */
        if ($searchMediaFolder->getTotal() <= 0) {
            $mediaFolderRepositroy->create(
                [
                    [
                        'id' => Defaults::MEDIA_FOLDER_ID,
                        'name' => Defaults::MEDIA_FOLDER_NAME,
                        'useParentConfiguration' => false,
                        'defaultFolder' => [
                            'entity' => 'blur_elysium_slides',
                        ],
                        'configuration' => [
                            'createThumbnails' => true,
                            'keepAspectRatio' => true,
                            'mediaThumbnailSizes' => $thumbnailSizeIds,
                        ],
                    ],
                ],
                $context
            );
        }
    }
}
