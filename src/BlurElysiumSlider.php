<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Blur\BlurElysiumSlider\Bootstrap\Lifecycle;

class BlurElysiumSlider extends Plugin
{
    public function postInstall(InstallContext $installContext): void
    {
        /** @var ContainerInterface $container */
        $container = $this->container;

        $lifecycle = new Lifecycle( $container );
        $lifecycle->install( $installContext->getContext() );
    }
    
    public function uninstall( UninstallContext $uninstallContext ): void
    {
        /** @var ContainerInterface $container */
        $container = $this->container;

        $uninstallContext->setAutoMigrate( false ); /// disable auto migration execution
        $migrationCollection = $uninstallContext->getMigrationCollection(); /// get migration collection

        if ( $uninstallContext->keepUserData() === false ) {
            /// call updateDestructive and remove entity from database
            $migrationCollection->migrateDestructiveInPlace( 1624100471 ); 

            /// remove media folder and according default folder
            $lifecycle = new Lifecycle( $container );
            $lifecycle->uninstall( $uninstallContext->getContext() );
        } 
    }
}
