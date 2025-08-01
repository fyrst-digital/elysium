<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides;

/**
 * Trait for handling content settings in entities.
 * 
 * Provides methods for getting, setting, and manipulating content settings
 * similar to the EntityCustomFieldsTrait for custom fields.
 */
trait ContentSettingsTrait
{
    /**
     * @var array<mixed>|null
     */
    protected ?array $contentSettings = null;

    /**
     * Get the value of contentSettings
     *
     * @return array<mixed>|null
     */
    public function getContentSettings(): ?array
    {
        return $this->contentSettings;
    }

    /**
     * Get specific content settings values by keys.
     *
     * Returns an array with the field names as keys and the values as values will be returned.
     * If you pass multiple field names and one of the fields does not exist, the field will not be in the result.
     *
     * Example:
     * ```php
     * $entity->setContentSettings([
     *     'my_content_field' => 'value',
     *     'my_other_content_field' => 'value',
     * ]);
     *
     * $entity->getContentSettingsValues('my_content_field') === ['my_content_field' => 'value'];
     *
     * $entity->getContentSettingsValues('my_content_field', 'my_other_content_field') === [
     *    'my_content_field' => 'value',
     *    'my_other_content_field' => 'value',
     * ];
     *
     * $entity->getContentSettingsValues('my_content_field', 'my_other_content_field', 'my_third_content_field') === [
     *    'my_content_field' => 'value',
     *    'my_other_content_field' => 'value',
     * ];
     * ```
     *
     * @param string ...$fields
     * @return array<string, mixed>
     */
    public function getContentSettingsValues(string ...$fields): array
    {
        return \array_intersect_key($this->contentSettings ?? [], \array_flip($fields));
    }

    /**
     * Get a single content setting value by key.
     *
     * If the field does not exist, null will be returned.
     *
     * Example:
     * ```php
     * $entity->getContentSettingsValue('my_content_field') === 'value';
     * ```
     *
     * @param string $field
     * @return mixed
     */
    public function getContentSettingsValue(string $field): mixed
    {
        return $this->contentSettings[$field] ?? null;
    }

    /**
     * Get a translated content setting value by key.
     *
     * @param string $field
     * @return mixed
     */
    public function getTranslatedContentSettingsValue(string $field): mixed
    {
        return $this->translated['contentSettings'][$field] ?? null;
    }

    /**
     * Set the value of contentSettings
     *
     * @param array<mixed>|null $contentSettings
     */
    public function setContentSettings(?array $contentSettings): void
    {
        $this->contentSettings = $contentSettings;
    }

    /**
     * Change content settings values.
     *
     * Allows to change content settings. If you pass only one field name, the value of the field will be changed.
     * If you pass multiple field names, an array with the field names as keys and the values as values will be changed.
     *
     * Example:
     * ```php
     * $entity->setContentSettings([
     *      'my_content_field' => 'value',
     *      'my_other_content_field' => 'value',
     * ]);
     *
     * $entity->changeContentSettings([
     *      'my_content_field' => 'new value',
     * ]);
     *
     * $entity->getContentSettingsValue('my_content_field') === 'new value';
     *
     * $entity->changeContentSettings([
     *      'my_content_field' => 'new value',
     *      'my_other_content_field' => 'new value',
     * ]);
     *
     * $entity->getContentSettingsValues('my_content_field', 'my_other_content_field') === [
     *      'my_content_field' => 'new value',
     *      'my_other_content_field' => 'new value',
     * ];
     * ```
     *
     * @param array<string, mixed> $contentSettings
     */
    public function changeContentSettings(array $contentSettings): void
    {
        $this->contentSettings = \array_replace(
            $this->contentSettings ?? [],
            $contentSettings
        );
    }
}
