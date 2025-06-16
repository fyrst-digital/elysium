<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ElysiumSlidesEntity>
 */
class ElysiumSlidesCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return ElysiumSlidesEntity::class;
    }

    public function getApiAlias(): string
    {
        return 'elysium_slides_collection';
    }
}
