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

class ElysiumSlidesAdminSearchIndexer extends AbstractAdminIndexer
{
    /**
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
        /**
         * Original implementation using GROUP_CONCAT - kept for reference
         * Uses MySQL string aggregation which may cause memory issues
         * with large numbers of translations per slide
         *
         * $data = $this->connection->fetchAllAssociative(
         *     'SELECT LOWER(HEX(elysium_slides.id)) as id,
         *             GROUP_CONCAT(DISTINCT translation.name SEPARATOR " ") as name,
         *             GROUP_CONCAT(DISTINCT translation.title SEPARATOR " ") as title
         *      FROM blur_elysium_slides elysium_slides
         *         INNER JOIN blur_elysium_slides_translation translation
         *             ON elysium_slides.id = translation.blur_elysium_slides_id
         *      WHERE elysium_slides.id IN (:ids)
         *      GROUP BY elysium_slides.id',
         *     ['ids' => Uuid::fromHexToBytesList($ids)],
         *     ['ids' => ArrayParameterType::BINARY]
         * );
         */

        // New implementation: Fetch translations separately and aggregate in PHP
        // This approach scales better with varying numbers of translations per slide
        $translations = $this->connection->fetchAllAssociative(
            'SELECT LOWER(HEX(blur_elysium_slides_id)) as slide_id, 
                    name, 
                    title
             FROM blur_elysium_slides_translation 
             WHERE blur_elysium_slides_id IN (:ids)',
            ['ids' => Uuid::fromHexToBytesList($ids)],
            ['ids' => ArrayParameterType::BINARY]
        );

        // Group translations by slide_id
        $grouped = [];
        foreach ($translations as $row) {
            $slideId = (string) $row['slide_id'];
            if (!isset($grouped[$slideId])) {
                $grouped[$slideId] = [];
            }
            $grouped[$slideId][] = $row['name'] . ' ' . $row['title'];
        }

        // Build mapped array with aggregated text
        $mapped = [];
        foreach ($ids as $id) {
            $slideId = strtolower($id);
            $text = isset($grouped[$slideId])
                ? \implode(' ', $grouped[$slideId])
                : '';
            $mapped[$slideId] = ['id' => $slideId, 'text' => \strtolower($text)];
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
