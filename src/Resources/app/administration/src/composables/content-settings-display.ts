import type { ContentSettings } from '@elysium/types/slide';

const { Store } = Shopware;

/**
 * Check if a value is considered "empty" for fallback purposes.
 * Empty string, null, and undefined are treated as missing.
 */
function isEmptyValue(value: unknown): boolean {
    return value === '' || value === null || value === undefined;
}

/**
 * Deep-merge source over target.
 * Only overwrites target keys where source has a non-empty value.
 * Mutates target in place.
 *
 * @warning This merge algorithm must stay in sync with the backend fallback
 * logic in {@see ElysiumSlidesHydrator}. Both use the same rules:
 * null and empty-string are skipped (fall back), objects are deep-merged
 * recursively, and all other non-empty values overwrite.
 */
function deepMerge(target: Record<string, unknown>, source: Record<string, unknown>): void {
    Object.entries(source).forEach(([key, value]) => {
        if (isEmptyValue(value)) {
            return;
        }

        if (
            typeof value === 'object' &&
            value !== null &&
            !Array.isArray(value) &&
            typeof target[key] === 'object' &&
            target[key] !== null &&
            !Array.isArray(target[key])
        ) {
            deepMerge(target[key] as Record<string, unknown>, value as Record<string, unknown>);
        } else {
            target[key] = value;
        }
    });
}

function getTranslations(slide: Record<string, unknown>): Record<string, unknown>[] {
    if (!slide.translations) {
        return [];
    }

    return Array.isArray(slide.translations)
        ? slide.translations
        : Object.values(slide.translations as Record<string, unknown>);
}

/**
 * Get the current language translation row from the slide's translations.
 * Returns the raw translation object for the current UI language, or null.
 */
export function getCurrentLanguageTranslation(slide: Record<string, unknown>): Record<string, unknown> | null {
    const context = Store.get('context');
    const currentLanguageId = context.api.languageId;

    if (!currentLanguageId || !slide.translations) {
        return null;
    }

    const translations = getTranslations(slide);
    const currentTranslation = translations.find((t: unknown) => {
        const translation = t as Record<string, unknown>;
        return translation.languageId === currentLanguageId;
    }) as Record<string, unknown> | undefined;

    return currentTranslation ?? null;
}

/**
 * Get the default language contentSettings from the slide's translations.
 */
export function getDefaultLanguageContentSettings(slide: Record<string, unknown>): ContentSettings | null {
    const context = Store.get('context');
    const systemLanguageId = context.api.systemLanguageId;

    if (!systemLanguageId || !slide.translations) {
        return null;
    }

    const translations = getTranslations(slide);
    const defaultTranslation = translations.find((t: unknown) => {
        const translation = t as Record<string, unknown>;
        return translation.languageId === systemLanguageId;
    }) as Record<string, unknown> | undefined;

    if (!defaultTranslation || !defaultTranslation.contentSettings) {
        return null;
    }

    return defaultTranslation.contentSettings as ContentSettings;
}

/**
 * Deep-merges default language contentSettings into current for display only.
 * Empty strings, null, and undefined in current fall back to default.
 * Returns a new object; does NOT mutate the original slide.
 */
export function getDisplayContentSettings(slide: Record<string, unknown>): ContentSettings {
    const current = (slide.contentSettings ?? {}) as ContentSettings;
    const defaultSettings = getDefaultLanguageContentSettings(slide);

    if (!defaultSettings) {
        return JSON.parse(JSON.stringify(current));
    }

    const merged = JSON.parse(JSON.stringify(defaultSettings)) as Record<string, unknown>;
    deepMerge(merged, JSON.parse(JSON.stringify(current)) as Record<string, unknown>);

    return merged as ContentSettings;
}

/**
 * Structured result indicating where a media value was inherited from.
 */
export interface MediaInheritanceSource {
    type: 'language' | 'device' | null;
    sourceKey: string | null;
}

const deviceCascades: Record<string, string[]> = {
    tabletId: ['mobileId'],
    desktopId: ['tabletId', 'mobileId'],
};

/**
 * Determine the inheritance source for a slide cover media field.
 *
 * Checks the exact device first, then walks through smaller devices
 * (mobile → tablet → desktop) in both the current and default language.
 *
 * @param slide - The slide entity
 * @param deviceKey - The key for the current device (e.g. 'mobileId', 'tabletId', 'desktopId')
 * @param isDefaultLanguage - Whether the current UI language is the default language
 */
export function getCoverInheritanceSource(
    slide: Record<string, unknown>,
    deviceKey: string,
    isDefaultLanguage: boolean,
): MediaInheritanceSource {
    const currentTranslation = getCurrentLanguageTranslation(slide);
    const currentCover = currentTranslation?.contentSettings?.slideCover ?? {};
    const defaultSettings = isDefaultLanguage ? null : getDefaultLanguageContentSettings(slide);
    const defaultCover = defaultSettings?.slideCover ?? {};

    // 1. Exact match in current language
    if (currentCover[deviceKey]) {
        return { type: null, sourceKey: null };
    }

    // 2. Cascade through smaller devices in current language
    const cascade = deviceCascades[deviceKey] ?? [];
    for (const key of cascade) {
        if (currentCover[key]) {
            return { type: 'device', sourceKey: key };
        }
    }

    // 3. Exact match in default language
    if (defaultCover[deviceKey]) {
        return { type: 'language', sourceKey: deviceKey };
    }

    // 4. Cascade through smaller devices in default language
    for (const key of cascade) {
        if (defaultCover[key]) {
            return { type: 'language', sourceKey: key };
        }
    }

    return { type: null, sourceKey: null };
}

/**
 * Determine the inheritance source for the slide cover video.
 */
export function getVideoInheritanceSource(
    slide: Record<string, unknown>,
    isDefaultLanguage: boolean,
): MediaInheritanceSource {
    const currentTranslation = getCurrentLanguageTranslation(slide);
    const currentVideo = currentTranslation?.contentSettings?.slideCover?.videoId ?? null;
    const defaultVideo = isDefaultLanguage
        ? null
        : (getDefaultLanguageContentSettings(slide)?.slideCover?.videoId ?? null);

    if (currentVideo) {
        return { type: null, sourceKey: null };
    }

    if (defaultVideo) {
        return { type: 'language', sourceKey: 'videoId' };
    }

    return { type: null, sourceKey: null };
}

/**
 * Determine the inheritance source for the focus image.
 */
export function getFocusImageInheritanceSource(
    slide: Record<string, unknown>,
    isDefaultLanguage: boolean,
): MediaInheritanceSource {
    const currentTranslation = getCurrentLanguageTranslation(slide);
    const currentId = currentTranslation?.contentSettings?.focusImageId ?? null;
    const defaultId = isDefaultLanguage
        ? null
        : (getDefaultLanguageContentSettings(slide)?.focusImageId ?? null);

    if (currentId) {
        return { type: null, sourceKey: null };
    }

    if (defaultId) {
        return { type: 'language', sourceKey: 'focusImageId' };
    }

    return { type: null, sourceKey: null };
}

/**
 * Gets a value from contentSettings by dot-notated path.
 * e.g. 'title', 'button.label', 'slideCover.alt'
 */
function getValueByPath(obj: Record<string, unknown>, path: string): unknown {
    return path.split('.').reduce<unknown>((acc, key) => {
        if (acc === null || acc === undefined) {
            return undefined;
        }
        return (acc as Record<string, unknown>)[key];
    }, obj);
}

/**
 * Gets fallback text for an input placeholder, matching Shopware's placeholder mixin pattern.
 *
 * Returns the current language value if it exists and is non-empty.
 * Otherwise returns the default language fallback value if it exists and is non-empty.
 * Otherwise returns the provided fallback snippet.
 *
 * @param slide - The slide entity
 * @param path - Dot-notated path within contentSettings (e.g. 'title', 'button.label', 'slideCover.alt')
 * @param fallback - Default text snippet if both current and default are empty
 */
export function getContentSettingsPlaceholder(
    slide: Record<string, unknown>,
    path: string,
    fallback?: string,
): string {
    const current = (slide.contentSettings ?? {}) as Record<string, unknown>;
    const currentValue = getValueByPath(current, path);

    if (!isEmptyValue(currentValue)) {
        return String(currentValue);
    }

    const defaultSettings = getDefaultLanguageContentSettings(slide);
    if (defaultSettings) {
        const defaultValue = getValueByPath(defaultSettings as Record<string, unknown>, path);
        if (!isEmptyValue(defaultValue)) {
            return String(defaultValue);
        }
    }

    return fallback ?? '';
}
