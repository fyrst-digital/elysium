<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Extension\Content\Media;

use Shopware\Core\Content\Media\MediaDefinition;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SetNullOnDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class MediaDefinitionExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField('blurElysiumSlidess', ElysiumSlidesDefinition::class, 'media_id', 'id'))->addFlags(new ApiAware(), new SetNullOnDelete())
            // new OneToOneAssociationField('exampleExtension', 'id', 'product_id', ExampleExtensionDefinition::class, true)
        );
    }

    public function getDefinitionClass(): string
    {
        return MediaDefinition::class;
    }
}