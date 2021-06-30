<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class BlurElysiumSlider extends Plugin
{
    
    public function uninstall( UninstallContext $uninstallContext ): void
    {
        $uninstallContext->setAutoMigrate( false ); // disable auto migration execution
        $migrationCollection = $uninstallContext->getMigrationCollection(); // get migration collection

        if ( $uninstallContext->keepUserData() === false ) {
            $migrationCollection->migrateDestructiveInPlace( 1624100471 ); // call updateDestructive
        } 
    }
}
