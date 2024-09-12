<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;

/**
 * @extends EntityCollection<ElysiumSlidesEntity>
 */
class ElysiumSlidesCollection extends EntityCollection
{
    function getExpectedClass(): string
    {
        return ElysiumSlidesEntity::class;
    }
}
