<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\SalesChannel;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\Events\ElysiumSlidesCriteriaEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ElysiumSlideLoader implements ElysiumSlideLoaderInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly AbstractElysiumSlideRoute $slideRoute,
    ) {}

    public function load(array $slideIds, ?Criteria $criteria, SalesChannelContext $context, ?string $identifier = null): ElysiumSlidesCollection
    {
        $criteria ??= new Criteria();

        if (!empty($slideIds)) {
            $criteria->setIds($slideIds);
        }

        $this->eventDispatcher->dispatch(
            new ElysiumSlidesCriteriaEvent($criteria, $context, $identifier)
        );

        $response = $this->slideRoute->load($criteria, $context, $identifier);

        return $response->getSlides();
    }
}
