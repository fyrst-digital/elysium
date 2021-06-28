<?php declare(strict_types=1);

namespace Blur\ElysiumBlocks\Core\Content\ElysiumSlides;

use Blur\ElysiumBlocks\Core\Content\ElysiumSlides\ElysiumSlidesEntity;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Blur\ElysiumBlocks\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation\ElysiumSlidesTranslationDefinition;

class ElysiumSlidesDefinition extends EntityDefinition {

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

            (new IdField( 'id', 'id' ))->addFlags( 
                new Required(), 
                new PrimaryKey() 
            ),
            // media association
            (new FkField('media_id', 'mediaId', MediaDefinition::class))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', true))->addFlags(new ApiAware()),
            // translation
            (new TranslatedField('name'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('title'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('description'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('button_label'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('url'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslationsAssociationField( 
                ElysiumSlidesTranslationDefinition::class, 
                'blur_elysium_slides_id'
            ))->addFlags(new ApiAware(), new Inherited(), new Required())
        ]);
    }
}