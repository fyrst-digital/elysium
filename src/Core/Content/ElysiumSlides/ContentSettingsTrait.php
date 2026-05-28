<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Core\Content\ElysiumSlides;

/**
 * Trait for handling content settings in entities.
 *
 * Provides methods for getting, setting, and manipulating content settings.
 *
 * Translation fallback for contentSettings is handled at hydration time by
 * {@see ElysiumSlidesHydrator}, which deep-merges the JSON blob across the
 * language chain. Therefore accessors in this trait read directly from the
 * already-merged `$this->contentSettings`.
 *
 * ContentSettings schema:
 * {
 *   "title": "Headline Text",
 *   "description": "<p>Rich text...</p>",
 *   "button": { "label": "Click here" },
 *   "url": "/some-path",
 *   "focusImageId": "uuid...",
 *   "slideCover": {
 *     "mobileId": "uuid...",
 *     "tabletId": "uuid...",
 *     "desktopId": "uuid...",
 *     "videoId": "uuid...",
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
     * Returns an array with the field names as keys and the values as values.
     * If you pass multiple field names and one of the fields does not exist,
     * the field will not be in the result.
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
        return $this->getContentSettingsValue('title');
    }

    public function getContentDescription(): ?string
    {
        return $this->getContentSettingsValue('description');
    }

    public function getContentButtonLabel(): ?string
    {
        return $this->getContentSettingsValue('button')['label'] ?? null;
    }

    public function getContentUrl(): ?string
    {
        return $this->getContentSettingsValue('url');
    }

    // --- Media ID accessors ---

    public function getContentSlideCoverMobileId(): ?string
    {
        return $this->getContentSettingsValue('slideCover')['mobileId'] ?? null;
    }

    public function getContentSlideCoverTabletId(): ?string
    {
        return $this->getContentSettingsValue('slideCover')['tabletId'] ?? null;
    }

    public function getContentSlideCoverDesktopId(): ?string
    {
        return $this->getContentSettingsValue('slideCover')['desktopId'] ?? null;
    }

    public function getContentSlideCoverVideoId(): ?string
    {
        return $this->getContentSettingsValue('slideCover')['videoId'] ?? null;
    }

    public function getContentFocusImageId(): ?string
    {
        return $this->getContentSettingsValue('focusImageId');
    }

    public function getContentSlideCoverAlt(): ?string
    {
        return $this->getContentSettingsValue('slideCover')['alt'] ?? null;
    }

    public function getContentSlideCoverTitle(): ?string
    {
        return $this->getContentSettingsValue('slideCover')['title'] ?? null;
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
