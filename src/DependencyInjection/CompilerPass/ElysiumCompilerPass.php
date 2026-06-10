<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\DependencyInjection\CompilerPass;

use Blur\BlurElysiumSlider\Defaults;
use Shopware\Core\Framework\Feature;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class ElysiumCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $featureFlags = $container->getParameter('shopware.feature.flags');
        if (is_array($featureFlags)) {
            $mergedFeatures = array_merge($featureFlags, Defaults::FEATURES);
            $container->setParameter('shopware.feature.flags', $mergedFeatures);
            Feature::registerFeatures($mergedFeatures);
        }

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../Resources/config')
        );

        if (Feature::isActive('elysium_preview_elasticsearch')) {
            $loader->load('elasticsearch.xml');
        }

        $sets = $container->hasParameter('shopware.html_sanitizer.sets') ? $container->getParameter('shopware.html_sanitizer.sets') : [];
        $sets['blur_elysium_headline'] = [
            'tags' => ['br', 'wbr', 'i', 'b', 'u', 'strong', 'span'],
            'attributes' => [],
            'custom_attributes' => [],
            'options' => [],
        ];
        $container->setParameter('shopware.html_sanitizer.sets', $sets);

        $fields = $container->hasParameter('shopware.html_sanitizer.fields') ? $container->getParameter('shopware.html_sanitizer.fields') : [];
        $fields['blur_elysium_slides_translation.title'] = [
            'sets' => ['blur_elysium_headline'],
        ];
        $container->setParameter('shopware.html_sanitizer.fields', $fields);
    }
}
