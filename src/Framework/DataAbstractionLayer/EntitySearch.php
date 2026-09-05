<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Framework\DataAbstractionLayer;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

/**
 * Reads repository search results without type-hinting EntitySearchResult.
 * Shopware marks that class deprecated for a v6.8.0 hierarchy change;
 * getEntities() and getTotal() remain the supported API.
 *
 * @phpstan-type SearchPayload array{entities: EntityCollection, total: int}
 */
final class EntitySearch
{
    /**
     * @template T of EntityCollection
     * @param EntityRepository<T> $repository
     * @return T
     */
    public static function entities(EntityRepository $repository, Criteria $criteria, Context $context): EntityCollection
    {
        return self::unwrap($repository->search($criteria, $context))['entities'];
    }

    /**
     * @template T of EntityCollection
     * @param EntityRepository<T> $repository
     * @return SearchPayload
     */
    public static function fetch(EntityRepository $repository, Criteria $criteria, Context $context): array
    {
        return self::unwrap($repository->search($criteria, $context));
    }

    /**
     * @return SearchPayload
     */
    private static function unwrap(object $result): array
    {
        if (!method_exists($result, 'getEntities') || !method_exists($result, 'getTotal')) {
            throw new \LogicException('Expected a DAL search result with getEntities() and getTotal().');
        }

        /** @var EntityCollection $entities */
        $entities = $result->getEntities();
        /** @var int $total */
        $total = $result->getTotal();

        return [
            'entities' => $entities,
            'total' => $total,
        ];
    }
}
