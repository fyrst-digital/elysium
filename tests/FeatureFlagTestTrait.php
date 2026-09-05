<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Tests;

use Shopware\Core\Framework\Feature;

/**
 * Registers plugin feature flags without wiping Shopware's registry.
 *
 * Shopware 6.7.13 DAL calls Feature::isActive('v6.8.0.0') when compiling
 * EntityDefinition. An empty or plugin-only registry then triggers
 * "Unknown feature V6_8_0_0" (PHPUnit failOnWarning).
 */
trait FeatureFlagTestTrait
{
    /**
     * @var array<string, mixed>
     */
    private array $previousRegisteredFeatures = [];

    protected function enablePluginFeatureFlags(string ...$features): void
    {
        $this->previousRegisteredFeatures = Feature::getRegisteredFeatures();

        Feature::registerFeature('v6.8.0.0', [
            'default' => false,
            'major' => true,
        ]);

        foreach ($features as $feature) {
            Feature::registerFeature($feature, ['default' => true]);
        }
    }

    protected function restorePluginFeatureFlags(): void
    {
        Feature::resetRegisteredFeatures();
        Feature::registerFeatures($this->previousRegisteredFeatures);
    }
}
