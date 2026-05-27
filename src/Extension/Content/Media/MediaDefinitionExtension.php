<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Extension\Content\Media;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class MediaDefinitionExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        // Media IDs are now stored in the contentSettings JSON field.
        // Reverse associations from Media -> Slides are no longer needed.
    }

    public function getDefinitionClass(): string
    {
        return MediaDefinition::class;
    }

    public function getEntityName(): string
    {
        return MediaDefinition::ENTITY_NAME;
    }
}
