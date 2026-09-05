<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation\ElysiumSlidesTranslationCollection;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation\ElysiumSlidesTranslationEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ElysiumSlidesTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'blur_elysium_slides_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getParentDefinitionClass(): string
    {
        return ElysiumSlidesDefinition::class;
    }

    public function getEntityClass(): string
    {
        return ElysiumSlidesTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ElysiumSlidesTranslationCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required()),
            (new JsonField('content_settings', 'contentSettings'))->addFlags(new ApiAware()),
            (new CustomFields())->addFlags(new ApiAware()),
        ]);
    }
}
