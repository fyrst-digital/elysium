<?php declare(strict_types=1);

use Shopware\Core\TestBootstrapper;

$loader = (new TestBootstrapper())
    ->addCallingPlugin()
    ->addActivePlugins('BlurElysiumSlider')
    ->setForceInstallPlugins(true)
    ->bootstrap()
    ->getClassLoader();

// Register plugin namespace (fallback for CI where composer doesn't know about the plugin)
$loader->addPsr4('Blur\\BlurElysiumSlider\\', __DIR__ . '/../src');
$loader->addPsr4('Blur\\BlurElysiumSlider\\Tests\\', __DIR__);
