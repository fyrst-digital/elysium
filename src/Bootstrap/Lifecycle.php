<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Bootstrap;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class Lifecycle
{
    /** @var ContainerInterface */
    private $container;

    /** @var EntityRepositoryInterface */
    private $mediaFolderRepositroy;

    /** @var EntityRepositoryInterface */
    private $mediaDefaultFolderRepositroy;

    /** @var EntityRepositoryInterface */
    private $mediaFolderConfigurationRepositroy;

    private const MEDIA_FOLDER_NAME = 'Elysium Slides';

    /** @var string */
    private $mediaFolderId;

    /** @var string */
    private $mediaDefaultFolderId;

    /** @var string */
    private $mediaFolderConfigurationId;

    public function __construct(
        ContainerInterface $container
    )
    {
        $this->container = $container;
        $this->mediaFolderRepositroy = $container->get('media_folder.repository');
        $this->mediaDefaultFolderRepositroy = $container->get('media_default_folder.repository');
        $this->mediaFolderConfigurationRepositroy = $container->get('media_folder_configuration.repository');
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

        # get associated mediaDefaultFolderId, mediaFolderId, mediaConfigurationId
        $mediaFolderElysiumSlides = $this->mediaDefaultFolderRepositroy->search($criteria, $context)->first();

        if ($mediaFolderElysiumSlides === null) {
            return;
        }

        # existence check
        if ( $mediaFolderElysiumSlides->getId() ) {
            $this->setMediaDefaultFolderId( $mediaFolderElysiumSlides->getId() );
        }

        # existence check
        if ( $mediaFolderElysiumSlides->getFolder() !== null ) {
            $this->setMediaFolderId( $mediaFolderElysiumSlides->getFolder()->getId() );
            $this->setMediaFolderConfigurationId( $mediaFolderElysiumSlides->getFolder()->getConfigurationId() );
        }

        if ( !empty( $this->getMediaFolderId() )) {
            # delete media folder entry
            $this->mediaFolderRepositroy->delete( [ [ 'id' => $this->getMediaFolderId() ] ], $context );
        }

        if ( !empty( $this->getMediaDefaultFolderId() )) {
            # delete media folder entry
            $this->mediaDefaultFolderRepositroy->delete( [ [ 'id' => $this->getMediaDefaultFolderId() ] ], $context );
        }

        if ( !empty( $this->getMediaFolderConfigurationId() )) {
            # delete media folder configuration entry
            $this->mediaFolderConfigurationRepositroy->delete( [ [ 'id' => $this->getMediaFolderConfigurationId() ] ], $context );
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

        if ( $this->mediaDefaultFolderRepositroy->search($criteria, $context)->getTotal() <= 0 ) {
            $this->mediaDefaultFolderRepositroy->create( [
                [
                    'id' => $this->getMediaDefaultFolderId(),
                    'associationFields' => ["media", "mediaPortrait"],
                    'entity' => 'blur_elysium_slides'
                ]
            ], $context);
        } 
    }

    private function createMediaFolder( 
        Context $context 
    ): void
    {
        $this->mediaFolderRepositroy->create( [
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

    /**
     * Get the value of mediaFolderId
     */ 
    private function getMediaFolderId(): string
    {
        return $this->mediaFolderId;
    }

    /**
     * Set the value of mediaFolderId
     *
     * @return  self
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
     * @return  self
     */ 
    private function setMediaDefaultFolderId(string $mediaDefaultFolderId): void
    {
        $this->mediaDefaultFolderId = $mediaDefaultFolderId;
    }

    /**
     * Get the value of mediaFolderConfigurationId
     */ 
    private function getMediaFolderConfigurationId(): string
    {
        return $this->mediaFolderConfigurationId;
    }

    /**
     * Set the value of mediaFolderConfigurationId
     *
     * @return  self
     */ 
    private function setMediaFolderConfigurationId(string $mediaFolderConfigurationId): void
    {
        $this->mediaFolderConfigurationId = $mediaFolderConfigurationId;
    }
}