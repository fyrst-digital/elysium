<?php declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Framework\DataAbstractionLayer\Dbal;

use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesDefinition;
use Blur\BlurElysiumSlider\Core\Content\ElysiumSlides\ElysiumSlidesEntity;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\EntityDefinitionQueryHelper;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\EntityHydrator;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\Context;

class ElysiumSlidesHydrator extends EntityHydrator
{
    /**
     * @param array<mixed> $row
     */
    protected function assign(EntityDefinition $definition, Entity $entity, string $root, array $row, Context $context): Entity
    {
        $entity = parent::assign($definition, $entity, $root, $row, $context);

        if ($definition->getEntityName() !== ElysiumSlidesDefinition::ENTITY_NAME || !$entity instanceof ElysiumSlidesEntity) {
            return $entity;
        }

        // Admin API: keep raw current-language values in forms to prevent accidental fallback persistence.
        // This assumes that admin requests always carry an AdminApiSource. If a slide is ever loaded via
        // CLI or message queue in an admin context, the merge will still run — which is acceptable because
        // those paths are read-only and benefit from the resolved fallback values.
        if ($context->getSource() instanceof AdminApiSource) {
            return $entity;
        }

        $this->mergeContentSettings($entity, $row, $root, $context);

        return $entity;
    }

    /**
     * Deep-merges contentSettings across the translation chain.
     *
     * Shopware's DAL performs translation fallback at the blob level (COALESCE).
     * For JSON fields this means missing keys inside a partial translation are never
     * backfilled from fallback languages. This method replicates the core's
     * custom-fields merge logic for contentSettings.
     *
     * Empty strings ("") are treated as missing values and fall back, matching the
     * plugin's editorial expectations.
     *
     * @warning This merge algorithm must stay in sync with the frontend fallback
     * logic in {@see content-settings-display.ts}. Both use the same rules:
     * null and empty-string are skipped (fall back), objects are deep-merged
     * recursively, and all other non-empty values overwrite.
     *
     * @param array<mixed> $row
     */
    private function mergeContentSettings(ElysiumSlidesEntity $entity, array $row, string $root, Context $context): void
    {
        // ElysiumSlidesDefinition is not inheritance-aware
        // buildTranslationChain returns [current, fallback, ...]
        // Reverse so fallback is merged first, then current language wins
        $chain = array_reverse(EntityDefinitionQueryHelper::buildTranslationChain($root, $context, false));

        $merged = [];

        foreach ($chain as $accessor) {
            $raw = self::value($row, $accessor, 'contentSettings');
            if ($raw === null) {
                continue;
            }

            try {
                $decoded = json_decode($raw, true, 512, \JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                continue;
            }

            if (!\is_array($decoded)) {
                continue;
            }

            $merged = $this->deepMergeCurrentOverFallback($merged, $decoded);
        }

        $entity->assign(['contentSettings' => $merged]);
        $entity->addTranslated('contentSettings', $merged);
    }

    /**
     * Recursively merges $current over $merged.
     *
     * - Non-empty scalar values in $current overwrite $merged.
     * - Empty strings and null are skipped (fall back to $merged).
     * - Arrays are deep-merged recursively.
     *
     * @param array<mixed> $merged
     * @param array<mixed> $current
     *
     * @return array<mixed>
     */
    private function deepMergeCurrentOverFallback(array $merged, array $current): array
    {
        foreach ($current as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (\is_array($value) && isset($merged[$key]) && \is_array($merged[$key])) {
                $merged[$key] = $this->deepMergeCurrentOverFallback($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
