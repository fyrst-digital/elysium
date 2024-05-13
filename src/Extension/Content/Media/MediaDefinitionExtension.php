<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Extension\Content\Media;

use Shopware\Core\Content\Media\MediaDefinition;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SetNullOnDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;


class MediaDefinitionExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField('blurElysiumSlides', ElysiumSlidesDefinition::class, 'slide_cover_id', 'id'))->addFlags(new ApiAware(), new SetNullOnDelete())
        );
        $collection->add(
            (new OneToManyAssociationField('blurElysiumSlides', ElysiumSlidesDefinition::class, 'slide_cover_mobile_id', 'id'))->addFlags(new ApiAware(), new SetNullOnDelete())
        );
        $collection->add(
            (new OneToManyAssociationField('blurElysiumSlides', ElysiumSlidesDefinition::class, 'slide_cover_tablet_id', 'id'))->addFlags(new ApiAware(), new SetNullOnDelete())
        );
        $collection->add(
            (new OneToManyAssociationField('blurElysiumSlides', ElysiumSlidesDefinition::class, 'slide_cover_video_id', 'id'))->addFlags(new ApiAware(), new SetNullOnDelete())
        );
        $collection->add(
            (new OneToManyAssociationField('blurElysiumSlides', ElysiumSlidesDefinition::class, 'presentation_media_id', 'id'))->addFlags(new ApiAware(), new SetNullOnDelete())
        );
    }

    public function getDefinitionClass(): string
    {
        return MediaDefinition::class;
    }
}
