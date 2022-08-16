<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider;

use Blur\BlurElysiumSlider\Bootstrap\Lifecycle;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class BlurElysiumSlider extends Plugin
{
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
            // $this->removeMediaFolders( $uninstallContext->getContext() );
            $lifecycle = new Lifecycle( $this->container );
            $lifecycle->uninstall( $uninstallContext->getContext() );
        } 
    }
}
