<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation\ElysiumSlidesTranslationDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;

class ElysiumSlidesDefinition extends EntityDefinition
{

    public const ENTITY_NAME = 'blur_elysium_slides';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ElysiumSlidesEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([

            (new IdField('id', 'id'))->addFlags(
                new Required(),
                new PrimaryKey()
            ),
            // product association
            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new ApiAware()),
            (new OneToOneAssociationField('product', 'product_id', 'id', ProductDefinition::class, true))->addFlags(new ApiAware()),
            // media associations
            /// slide cover
            (new FkField('slide_cover_id', 'slideCoverId', MediaDefinition::class))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('slideCover', 'slide_cover_id', MediaDefinition::class, 'id', true))->addFlags(new ApiAware()),
            /// slide cover mobile
            (new FkField('slide_cover_mobile_id', 'slideCoverMobileId', MediaDefinition::class))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('slideCoverMobile', 'slide_cover_mobile_id', MediaDefinition::class, 'id', true))->addFlags(new ApiAware()),
            /// slide cover tablet
            (new FkField('slide_cover_tablet_id', 'slideCoverTabletId', MediaDefinition::class))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('slideCoverTablet', 'slide_cover_tablet_id', MediaDefinition::class, 'id', true))->addFlags(new ApiAware()),
            /// slide cover video
            (new FkField('slide_cover_video_id', 'slideCoverVideoId', MediaDefinition::class))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('slideCoverVideo', 'slide_cover_video_id', MediaDefinition::class, 'id', true))->addFlags(new ApiAware()),
            /// media presentation
            (new FkField('presentation_media_id', 'presentationMediaId', MediaDefinition::class))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('presentationMedia', 'presentation_media_id', MediaDefinition::class, 'id', true))->addFlags(new ApiAware()),
            // slide settings
            (new JsonField('slide_settings', 'slideSettings',))->addFlags(new ApiAware()),
            // translation
            (new TranslatedField('name'))->addFlags(new ApiAware(), new Required(), new Inherited()),
            (new TranslatedField('title'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('description'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('buttonLabel'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('url'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),
            (new TranslationsAssociationField(
                ElysiumSlidesTranslationDefinition::class,
                'blur_elysium_slides_id'
            ))->addFlags(new ApiAware(), new Inherited(), new Required())
        ]);
    }
}
