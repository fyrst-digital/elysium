<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Blur\BlurElysiumSlider\Bootstrap\Lifecycle;

class BlurElysiumSlider extends Plugin
{
    public function postInstall(InstallContext $installContext): void
    {
        /** @var ContainerInterface $container */
        $container = $this->container;

        $lifecycle = new Lifecycle($container);
        $lifecycle->postInstall($installContext);
    }

    public function postUpdate(UpdateContext $updateContext): void
    {
        /** @var ContainerInterface $container */
        $container = $this->container;

        $lifecycle = new Lifecycle($container);
        $lifecycle->postUpdate($updateContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        /** @var ContainerInterface $container */
        $container = $this->container;

        if ($uninstallContext->keepUserData() === false) {
            $lifecycle = new Lifecycle($container);
            $lifecycle->uninstall($uninstallContext->getContext());
        }
    }
}
