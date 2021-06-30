<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Aggregate\ElysiumSlidesTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;

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

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            ( new StringField( 'name', 'name' ) )->addFlags( new Required() ),
            ( new StringField( 'title', 'title' ) )->addFlags( new ApiAware() ),
            ( new LongTextField( 'description', 'description' ) )->addFlags( new ApiAware() ),
            ( new StringField( 'button_label', 'button_label' ) )->addFlags( new ApiAware() ),
            ( new LongTextField( 'url', 'url' ) )->addFlags( new ApiAware() )
        ]);
    }
}