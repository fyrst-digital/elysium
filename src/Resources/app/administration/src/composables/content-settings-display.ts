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

/**
 * Get the default language contentSettings from the slide's translations.
 */
function getDefaultLanguageContentSettings(slide: Record<string, unknown>): ContentSettings | null {
    const context = Store.get('context');
    const systemLanguageId = context.api.systemLanguageId;

    if (!systemLanguageId || !slide.translations) {
        return null;
    }

    const translations = Array.isArray(slide.translations)
        ? slide.translations
        : Object.values(slide.translations as Record<string, unknown>);

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
