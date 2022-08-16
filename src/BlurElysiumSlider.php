<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider;

use Blur\BlurElysiumSlider\Bootstrap\Lifecycle;

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


    public function postInstall(InstallContext $installContext): void
    {
        $lifecycle = new Lifecycle( $this->container );
        $lifecycle->install( $installContext->getContext() );
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

    public function removeMediaFolders( $context )
    {
        $mediaDefaultFolderId = null;
        $mediaFolderId = null;
        $mediaConfigurationId = null;

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
