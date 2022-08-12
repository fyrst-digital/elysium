<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class BlurElysiumSlider extends Plugin
{
    private $mediaFolderRepository;

    private $mediaDefaultFolderRepository;

    private const MEDIA_FOLDER_NAME = 'Elysium Slides';
    
    private const MEDIA_THUMBNAIL_SIZES = [
        [
            'width' => 500,
            'height' => 1000
        ], [
            'width' => 700,
            'height' => 1400
        ], [
            'width' => 1000,
            'height' => 1800
        ], [
            'width' => 2000,
            'height' => 2200,
        ]
    ];

    public function postInstall(InstallContext $installContext): void
    {
        $context = $installContext->getContext();
        $mediaFolderId = Uuid::randomHex();
        $mediaDefaultFolderId = Uuid::randomHex();

        $this->setMediaDefaultFolderRepository( $this->container->get('media_default_folder.repository') );
        $this->setMediaFolderRepository( $this->container->get('media_folder.repository') );
        
        // create media default folder entry
        $this->createMediaDefaultFolder( $mediaDefaultFolderId, $context);

        // create media folder entry 
        $this->createMediaFolder( $mediaFolderId, $mediaDefaultFolderId, $context );
    }
    
    public function uninstall( UninstallContext $uninstallContext ): void
    {
        $uninstallContext->setAutoMigrate( false ); // disable auto migration execution
        $migrationCollection = $uninstallContext->getMigrationCollection(); // get migration collection

        if ( $uninstallContext->keepUserData() === false ) {
            // call updateDestructive and remove entity from database
            $migrationCollection->migrateDestructiveInPlace( 1624100471 ); 

            // remove media folder and according default folder
            $this->removeMediaFolders( $uninstallContext->getContext() );
        } 
    }

    public function createMediaDefaultFolder( 
        $defaultFolderId, 
        Context $context 
    ): void
    {
        
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('entity', 'blur_elysium_slides'));
        $criteria->addAssociation('folder');
        $criteria->setLimit(1);

        if ( $this->getMediaDefaultFolderRepository()->search($criteria, $context)->getTotal() <= 0 ) {
            $this->getMediaDefaultFolderRepository()->create( [
                [
                    'id' => $defaultFolderId,
                    'associationFields' => ["media", "mediaPortrait"],
                    'entity' => 'blur_elysium_slides'
                ]
            ], $context);
        } 
    }

    public function createMediaFolder( 
        $mediaFolderId, 
        $defaultFolderId, 
        Context $context 
    ): void
    {
        
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('entity', 'blur_elysium_slides'));
        $criteria->addAssociation('folder');
        $criteria->setLimit(1);

        $this->getMediaFolderRepository()->create( [
            [
                'id' => $mediaFolderId,
                'name' => self::MEDIA_FOLDER_NAME,
                'useParentConfiguration' => false,
                'configuration' => [
                    'mediaThumbnailSizes' => self::MEDIA_THUMBNAIL_SIZES
                ], // @TODO set proper folder configuration. i.e. thumbnail sizes
                'defaultFolderId' => $defaultFolderId
            ]
        ], $context);
    }

    public function removeMediaFolders( $context )
    {
        $mediaDefaultFolderId = null;
        $mediaFolderId = null;
        $mediaConfigurationId = null;
        $mediaThumbnailSizes = null;

        $this->setMediaDefaultFolderRepository( $this->container->get('media_default_folder.repository') );
        $this->setMediaFolderRepository( $this->container->get('media_folder.repository') );

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('entity', 'blur_elysium_slides'));
        $criteria->addAssociation('media_folder');
        $criteria->setLimit(1);

        // get associated mediaDefaultFolderId, mediaFolderId, mediaConfigurationId
        $mediaFolderElysiumSlides = $this->getMediaDefaultFolderRepository()->search($criteria, $context)->first();


        if ($mediaFolderElysiumSlides === null) {
            return;
        }

        if ($mediaFolderElysiumSlides->id) {
            $mediaDefaultFolderId = $mediaFolderElysiumSlides->id;
        }

        // existence check
        if ( $mediaFolderElysiumSlides->folder !== null ) {
            $mediaFolderId = $mediaFolderElysiumSlides->folder->id;
            $mediaConfigurationId = $mediaFolderElysiumSlides->folder->configurationId;
        }

        $criteriaConfiguration = new Criteria();
        $criteriaConfiguration->addFilter(new EqualsFilter('id', $mediaConfigurationId));
        $criteriaConfiguration->addAssociation('media_folder_configuration_media_thumbnail_size');
        $criteriaConfiguration->setLimit(1);

        #dd($this->container->get('media_folder_configuration.repository')->search($criteriaConfiguration, $context)->first()->mediaThumbnailSizes);

        if ( $mediaThumbnailSizes !== null ) {
            // delete thumbnail sizes related to media config
        }

        #dd($mediaFolderId, $mediaDefaultFolderId);

        
        if ( $mediaFolderId !== null ) {
            // delete media folder entry
            $this->getMediaFolderRepository()->delete([
                [
                    'id' => $mediaFolderId,
                ]
            ], $context);
        }

        if ( $mediaDefaultFolderId !== null ) {
            // delete media default folder entry
            $this->getMediaDefaultFolderRepository()->delete([
                [ 
                    'id' => $mediaDefaultFolderId
                ]
            ], $context); 
        }

        if ( $mediaConfigurationId ) {
            // delete media folder configuration entry
            $this->container->get('media_folder_configuration.repository')->delete([
                [ 
                    'id' => $mediaConfigurationId
                ]
            ], $context);
        }
    }

    /**
     * Get the value of mediaFolderRepository
     */ 
    public function getMediaFolderRepository(): EntityRepositoryInterface
    {
        return $this->mediaFolderRepository;
    }

    /**
     * Set the value of mediaFolderRepository
     *
     * @return  self
     */ 
    public function setMediaFolderRepository( 
        EntityRepositoryInterface $mediaFolderRepository
    ): void
    {
        $this->mediaFolderRepository = $mediaFolderRepository;
    }

    /**
     * Get the value of mediaDefaultFolderRepository
     */ 
    public function getMediaDefaultFolderRepository(): EntityRepositoryInterface
    {
        return $this->mediaDefaultFolderRepository;
    }

    /**
     * Set the value of mediaDefaultFolderRepository
     *
     * @return  self
     */ 
    public function setMediaDefaultFolderRepository(
        EntityRepositoryInterface $mediaDefaultFolderRepository
    ): void
    {
        $this->mediaDefaultFolderRepository = $mediaDefaultFolderRepository;
    }
}
