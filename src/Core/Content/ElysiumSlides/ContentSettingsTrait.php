<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides;

/**
 * Trait for handling content settings in entities.
 *
 * Provides methods for getting, setting, and manipulating content settings
 * similar to the EntityCustomFieldsTrait for custom fields.
 *
 * ContentSettings schema:
 * {
 *   "title": "Headline Text",
 *   "description": "<p>Rich text...</p>",
 *   "button": { "label": "Click here" },
 *   "url": "/some-path",
 *   "focusImageId": "uuid...",
 *   "slideCover": {
 *     "mobileId": "uuid...",    // primary / fallback base
 *     "tabletId": "uuid...",    // optional, falls back to mobile
 *     "desktopId": "uuid...",   // optional, falls back to tablet -> mobile
 *     "videoId": "uuid...",     // optional, overrides image when present
 *     "alt": "Alt text",
 *     "title": "Title text"
 *   }
 * }
 */
trait ContentSettingsTrait
{
    /**
     * @var array<mixed>|null
     */
    protected ?array $contentSettings = [];

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
     * @param array<string, mixed> $contentSettings
     */
    public function changeContentSettings(array $contentSettings): void
    {
        $this->contentSettings = \array_replace(
            $this->contentSettings ?? [],
            $contentSettings
        );
    }

    // --- Content accessors ---

    public function getContentTitle(): ?string
    {
        return $this->getTranslatedContentSettingsValue('title')
            ?? $this->getContentSettingsValue('title');
    }

    public function getContentDescription(): ?string
    {
        return $this->getTranslatedContentSettingsValue('description')
            ?? $this->getContentSettingsValue('description');
    }

    public function getContentButtonLabel(): ?string
    {
        return $this->getTranslatedContentSettingsValue('button')['label']
            ?? $this->getContentSettingsValue('button')['label']
            ?? null;
    }

    public function getContentUrl(): ?string
    {
        return $this->getTranslatedContentSettingsValue('url')
            ?? $this->getContentSettingsValue('url');
    }

    // --- Media ID accessors ---

    /**
     * Get the primary (mobile) cover image ID.
     */
    public function getContentSlideCoverMobileId(): ?string
    {
        return $this->getTranslatedContentSettingsValue('slideCover')['mobileId']
            ?? $this->getContentSettingsValue('slideCover')['mobileId']
            ?? null;
    }

    public function getContentSlideCoverTabletId(): ?string
    {
        return $this->getTranslatedContentSettingsValue('slideCover')['tabletId']
            ?? $this->getContentSettingsValue('slideCover')['tabletId']
            ?? null;
    }

    public function getContentSlideCoverDesktopId(): ?string
    {
        return $this->getTranslatedContentSettingsValue('slideCover')['desktopId']
            ?? $this->getContentSettingsValue('slideCover')['desktopId']
            ?? null;
    }

    public function getContentSlideCoverVideoId(): ?string
    {
        return $this->getTranslatedContentSettingsValue('slideCover')['videoId']
            ?? $this->getContentSettingsValue('slideCover')['videoId']
            ?? null;
    }

    public function getContentFocusImageId(): ?string
    {
        return $this->getTranslatedContentSettingsValue('focusImageId')
            ?? $this->getContentSettingsValue('focusImageId');
    }

    /**
     * Get the slide cover alt text from contentSettings.
     */
    public function getContentSlideCoverAlt(): ?string
    {
        return $this->getTranslatedContentSettingsValue('slideCover')['alt']
            ?? $this->getContentSettingsValue('slideCover')['alt']
            ?? null;
    }

    /**
     * Get the slide cover title text from contentSettings.
     */
    public function getContentSlideCoverTitle(): ?string
    {
        return $this->getTranslatedContentSettingsValue('slideCover')['title']
            ?? $this->getContentSettingsValue('slideCover')['title']
            ?? null;
    }

    /**
     * Collect all non-null media IDs from contentSettings for batch resolution.
     *
     * @return string[]
     */
    public function getContentMediaIds(): array
    {
        $ids = [];

        $slideCover = $this->getContentSettingsValue('slideCover') ?? [];

        foreach (['mobileId', 'tabletId', 'desktopId', 'videoId'] as $key) {
            if (!empty($slideCover[$key])) {
                $ids[] = $slideCover[$key];
            }
        }

        $focusImageId = $this->getContentFocusImageId();
        if ($focusImageId !== null) {
            $ids[] = $focusImageId;
        }

        return array_values(array_unique($ids));
    }
}
