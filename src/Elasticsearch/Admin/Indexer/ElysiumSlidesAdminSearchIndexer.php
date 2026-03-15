<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Elasticsearch\Admin\Indexer;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesCollection;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IterableQuery;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Elasticsearch\Admin\Indexer\AbstractAdminIndexer;

final class ElysiumSlidesAdminSearchIndexer extends AbstractAdminIndexer
{
    /**
     * @internal
     *
     * @param EntityRepository<ElysiumSlidesCollection> $repository
     */
    public function __construct(
        private readonly Connection $connection,
        private readonly IteratorFactory $iteratorFactory,
        private readonly EntityRepository $repository,
        private readonly int $indexingBatchSize
    ) {}

    public function getDecorated(): AbstractAdminIndexer
    {
        throw new DecorationPatternException(self::class);
    }

    public function getEntity(): string
    {
        return ElysiumSlidesDefinition::ENTITY_NAME;
    }

    public function getName(): string
    {
        return ElysiumSlidesDefinition::ENTITY_NAME;
    }

    public function getIterator(): IterableQuery
    {
        return $this->iteratorFactory->createIterator($this->getEntity(), null, $this->indexingBatchSize);
    }

    public function fetch(array $ids): array
    {
        $data = $this->connection->fetchAllAssociative(
            'SELECT LOWER(HEX(elysium_slides.id)) as id,
                    GROUP_CONCAT(DISTINCT translation.name SEPARATOR " ") as name,
                    GROUP_CONCAT(DISTINCT translation.title SEPARATOR " ") as title
             FROM blur_elysium_slides elysium_slides
                INNER JOIN blur_elysium_slides_translation translation
                    ON elysium_slides.id = translation.blur_elysium_slides_id
             WHERE elysium_slides.id IN (:ids)
             GROUP BY elysium_slides.id',
            [
                'ids' => Uuid::fromHexToBytesList($ids),
            ],
            [
                'ids' => ArrayParameterType::BINARY,
            ]
        );

        $mapped = [];
        foreach ($data as $row) {
            $id = (string) $row['id'];
            $text = \implode(' ', array_filter($row));
            $mapped[$id] = ['id' => $id, 'text' => \strtolower($text)];
        }

        return $mapped;
    }

    public function globalData(array $result, Context $context): array
    {
        $ids = array_column($result['hits'], 'id');

        return [
            'total' => (int) $result['total'],
            'data' => $this->repository->search(new Criteria($ids), $context)->getEntities(),
        ];
    }
}
