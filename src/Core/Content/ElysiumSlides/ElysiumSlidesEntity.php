<?php declare(strict_types=1);

namespace Blur\ElysiumBlocks\Core\Content\ElysiumSlides;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ElysiumSlidesEntity extends Entity
{

    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected $label;

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel( ?string $label ): void
    {
        $this->label = $label;
    }
}