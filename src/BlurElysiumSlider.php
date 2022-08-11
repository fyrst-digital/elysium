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
        ], [
            'width' => 700,
        ], [
            'width' => 1000,
        ], [
            'width' => 2000,
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
    }
    
    public function uninstall( UninstallContext $uninstallContext ): void
    {
        $uninstallContext->setAutoMigrate( false ); // disable auto migration execution
        $migrationCollection = $uninstallContext->getMigrationCollection(); // get migration collection

        if ( $uninstallContext->keepUserData() === false ) {
            $migrationCollection->migrateDestructiveInPlace( 1624100471 ); // call updateDestructive
        } 
    }

    public function createMediaDefaultFolder( $defaultFolderId, Context $context )
    {
        $this->getMediaDefaultFolderRepository()->create( [
            'id' => $defaultFolderId,
            'associationFields' => ["media", "mediaPortrait"],
            'entity' => 'blur_elysium_slides'
        ], $context);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('entity', 'blur_elysium_slides'));
        $criteria->addAssociation('folder');
        $criteria->setLimit(1);

        dd( $this->getMediaDefaultFolderRepository()->search($criteria, $context) );
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
