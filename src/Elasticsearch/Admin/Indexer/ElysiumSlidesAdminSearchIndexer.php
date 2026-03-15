<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Elasticsearch\Admin\Indexer;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Elasticsearch\Admin\Indexer\AbstractAdminIndexer;

final class ElysiumSlidesAdminSearchIndexer extends AbstractAdminIndexer
{
    public function __construct(
        private readonly Connection $connection,
        private readonly IteratorFactory $iteratorFactory,
    ) {}

    public function getName(): string
    {
        return ElysiumSlidesDefinition::ENTITY_NAME;
    }

    public function getEntity(): string
    {
        return ElysiumSlidesDefinition::class;
    }

    public function getMapping(array $mapping): array
    {
        $mapping['properties'] = [
            'id' => self::KEYWORD_FIELD,
            'name' => self::SEARCH_FIELD,
            'title' => self::SEARCH_FIELD,
            'description' => self::SEARCH_FIELD,
            'url' => self::SEARCH_FIELD,
            'buttonLabel' => self::SEARCH_FIELD,
            'createdAt' => self::DATETIME_FIELD,
            'updatedAt' => self::DATETIME_FIELD,
        ];

        return $mapping;
    }

    public function getIterator(): iterable
    {
        $iterator = $this->iteratorFactory->createIterator($this->getEntity());

        foreach ($iterator as $id) {
            yield ['id' => Uuid::fromBytesToHex($id['id'])];
        }
    }

    public function fetch(array $ids, Context $context, Criteria $criteria): array
    {
        $bytes = array_map(fn(string $id) => Uuid::fromHexToBytes($id), $ids);

        $query = $this->connection->createQueryBuilder();
        $query->select([
            'LOWER(HEX(elysium_slides.id)) AS id',
            'COALESCE(translation.name, translation_fallback.name) AS name',
            'COALESCE(translation.title, translation_fallback.title) AS title',
            'COALESCE(translation.description, translation_fallback.description) AS description',
            'COALESCE(translation.url, translation_fallback.url) AS url',
            'COALESCE(translation.button_label, translation_fallback.button_label) AS buttonLabel',
            'elysium_slides.created_at AS createdAt',
            'elysium_slides.updated_at AS updatedAt',
        ]);
        $query->from('blur_elysium_slides', 'elysium_slides');
        $query->leftJoin(
            'elysium_slides',
            'blur_elysium_slides_translation',
            'translation',
            'elysium_slides.id = translation.blur_elysium_slides_id 
            AND translation.language_id = :languageId'
        );
        $query->leftJoin(
            'elysium_slides',
            'blur_elysium_slides_translation',
            'translation_fallback',
            'elysium_slides.id = translation_fallback.blur_elysium_slides_id 
            AND translation_fallback.language_id = :fallbackLanguageId'
        );
        $query->andWhere('elysium_slides.id IN (:ids)');
        $query->setParameter('ids', $bytes, Connection::PARAM_STR_ARRAY);
        $query->setParameter('languageId', Uuid::fromHexToBytes($context->getLanguageId()));
        $query->setParameter('fallbackLanguageId', Uuid::fromHexToBytes($context->getFallbackLanguageId()));

        return $query->executeQuery()->fetchAllAssociativeIndexed();
    }

    public function globalData(Context $context, Criteria $criteria): array
    {
        return [
            'total' => (int) $this->connection->fetchOne('SELECT COUNT(*) FROM blur_elysium_slides'),
        ];
    }
}
