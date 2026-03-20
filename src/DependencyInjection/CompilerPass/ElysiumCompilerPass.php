<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\DependencyInjection\CompilerPass;

use Shopware\Core\Framework\Feature;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class ElysiumCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../Resources/config')
        );

        if (Feature::isActive('elysium_preview_elasticsearch')) {
            $loader->load('elasticsearch.xml');
        }
    }
}
