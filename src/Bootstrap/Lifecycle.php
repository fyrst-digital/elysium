<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Bootstrap;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Content\Media\Aggregate\MediaDefaultFolder\MediaDefaultFolderEntity;
use Shopware\Core\Content\Media\Aggregate\MediaFolder\MediaFolderEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

class Lifecycle
{
    /** @var string */
    private const MEDIA_FOLDER_NAME = 'Elysium Slides';

    private string $mediaFolderId;

    private string $mediaDefaultFolderId;

    private ?string $mediaFolderConfigurationId;

    public function __construct(
        private readonly ContainerInterface $container
    )
    {
    }

    public function install( Context $context ): void
    {
        # create IDs
        $this->setMediaFolderId( Uuid::randomHex() );
        $this->setMediaDefaultFolderId( Uuid::randomHex() );

        # create media default folder entry
        $this->createMediaDefaultFolder( $context );

        # create media folder entry 
        $this->createMediaFolder( $context );
    }

    public function uninstall( Context $context ): void
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


        if ( $mediaFolderElysiumSlides->getId() ) {
            $this->setMediaDefaultFolderId( $mediaFolderElysiumSlides->getId() );
        }

        # existence check
        if ( $mediaFolderElysiumSlides->getFolder() !== null ) {
            /** @var MediaFolderEntity $elysiumSlidesFolder */
            $elysiumSlidesFolder = $mediaFolderElysiumSlides->getFolder();
            $this->setMediaFolderId( 
                $elysiumSlidesFolder->getId() 
            );
            $this->setMediaFolderConfigurationId( 
                $elysiumSlidesFolder->getConfigurationId() 
            );
        }

        if ( !empty( $this->getMediaFolderId() )) {
            $mediaFolderRepositroy->delete( [ [ 'id' => $this->getMediaFolderId() ] ], $context );
        }

        if ( !empty( $this->getMediaDefaultFolderId() )) {
            $mediaDefaultFolderRepositroy->delete( [ [ 'id' => $this->getMediaDefaultFolderId() ] ], $context );
        }

        if ( !empty( $this->getMediaFolderConfigurationId() )) {
            # delete media folder configuration entry
            $mediaFolderConfigurationRepositroy->delete( [ [ 'id' => $this->getMediaFolderConfigurationId() ] ], $context );
        }
    }

    private function createMediaDefaultFolder( 
        Context $context 
    ): void
    {
        
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('entity', 'blur_elysium_slides'));
        $criteria->addAssociation('folder');
        $criteria->setLimit(1);

        /** @var EntityRepository */
        $mediaDefaultFolderRepositroy = $this->container->get('media_default_folder.repository');
        
        /** @var EntitySearchResult */
        $mediaDefaultFolderResult = $mediaDefaultFolderRepositroy->search($criteria, $context);

        if ( $mediaDefaultFolderResult->getTotal() <= 0 ) {
            # create new media default for blur_elysium_slides
            $mediaDefaultFolderRepositroy->create( [
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
            $this->setMediaFolderId( $elysiumMediaFolder->getId() );
        }
    }

    private function createMediaFolder( 
        Context $context 
    ): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter( 'id', $this->getMediaFolderId() ));
        /** @var EntityRepository $mediaFolderRepositroy */
        $mediaFolderRepositroy = $this->container->get('media_folder.repository');

        if ( $mediaFolderRepositroy->search($criteria, $context)->getTotal() <= 0 ) {
            $mediaFolderRepositroy->create( [
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
    private function setMediaFolderId( string $mediaFolderId): void
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