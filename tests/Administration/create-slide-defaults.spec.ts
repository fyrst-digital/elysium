import assert from 'node:assert/strict';
import { before, test } from 'node:test';
import type { ContentSettings, ElysiumSlide, SlideSettings } from '../../src/Resources/app/administration/src/types/slide';

type PlainObject = Record<string, unknown>;
type MergeSlideSettings = (
    slide: ElysiumSlide,
    properties: Record<string, unknown>
) => ElysiumSlide;
type ApplyCreateSlideDefaults = (slide: ElysiumSlide) => ElysiumSlide;

function isPlainObject(value: unknown): value is PlainObject {
    return value !== null && typeof value === 'object' && !Array.isArray(value);
}

/**
 * Mutating merge, matching Shopware `Utils.object.deepMergeObject`
 * (lodash mergeWith that concatenates arrays).
 */
function deepMergeObject<T extends object, S extends object>(target: T, source: S): T & S {
    Object.keys(source).forEach((key) => {
        const srcValue = (source as PlainObject)[key];
        const targetRecord = target as PlainObject;
        const tgtValue = targetRecord[key];

        if (Array.isArray(tgtValue) && Array.isArray(srcValue)) {
            targetRecord[key] = tgtValue.concat(srcValue);
            return;
        }

        if (isPlainObject(tgtValue) && isPlainObject(srcValue)) {
            deepMergeObject(tgtValue, srcValue);
            return;
        }

        targetRecord[key] = srcValue;
    });

    return target as T & S;
}

let defaultSlideSettings: SlideSettings;
let defaultContentSettings: ContentSettings;
let applyCreateSlideDefaults: ApplyCreateSlideDefaults;
let mergeSlideSettings: MergeSlideSettings;

before(async () => {
    (globalThis as { Shopware: unknown }).Shopware = {
        Utils: {
            object: {
                deepMergeObject,
            },
        },
    };

    ({
        defaultSlideSettings,
        defaultContentSettings,
    } = await import(
        '../../src/Resources/app/administration/src/component/slide/settings.ts'
    ));

    ({
        applyCreateSlideDefaults,
        mergeSlideSettings,
    } = await import(
        '../../src/Resources/app/administration/src/component/slide/pages/detail/merge-settings.ts'
    ));
});

function productLinkedSlide(): ElysiumSlide {
    return {
        id: 'existing-slide',
        name: 'Existing slide',
        active: true,
        activeFrom: null,
        activeUntil: null,
        productId: 'product-1',
        product: { id: 'product-1' } as ElysiumSlide['product'],
        categoryId: 'category-1',
        category: { id: 'category-1' } as ElysiumSlide['category'],
        customFields: {},
        translated: { name: 'Existing slide' },
        slideSettings: {
            slide: {
                linking: {
                    type: 'product',
                    buttonAppearance: 'primary',
                    buttonSize: 'md',
                    openExternal: false,
                    overlay: false,
                    showProductFocusImage: true,
                    showProductTitle: true,
                    showProductDescription: true,
                    showCategoryFocusImage: true,
                    showCategoryTitle: true,
                    showCategoryDescription: true,
                },
            },
        } as ElysiumSlide['slideSettings'],
        contentSettings: {
            title: 'Previous headline',
            description: 'Previous description',
            button: {
                label: 'Buy now',
            },
            url: '/old-url',
            focusImageId: 'focus-media',
            slideCover: {
                mobileId: 'cover-mobile',
                tabletId: 'cover-tablet',
                desktopId: 'cover-desktop',
                videoId: 'cover-video',
                alt: 'Cover alt',
                title: 'Cover title',
            },
        },
    };
}

test('mergeSlideSettings does not mutate default slideSettings or contentSettings', () => {
    const loaded = productLinkedSlide();

    mergeSlideSettings(loaded, {
        slideSettings: defaultSlideSettings,
        contentSettings: defaultContentSettings,
    });

    assert.equal(loaded.slideSettings.slide.linking.type, 'product');
    assert.equal(loaded.contentSettings.title, 'Previous headline');
    assert.equal(loaded.productId, 'product-1');

    assert.equal(defaultSlideSettings.slide.linking.type, 'custom');
    assert.equal(defaultContentSettings.title, '');
    assert.equal(defaultContentSettings.description, '');
    assert.equal(defaultContentSettings.button.label, '');
    assert.equal(defaultContentSettings.url, '');
    assert.equal(defaultContentSettings.focusImageId, null);
    assert.equal(defaultContentSettings.slideCover.mobileId, null);
});

test('mergeSlideSettings assigns a clone when a settings object is missing', () => {
    const loaded = productLinkedSlide();
    delete (loaded as { contentSettings?: unknown }).contentSettings;

    mergeSlideSettings(loaded, {
        slideSettings: defaultSlideSettings,
        contentSettings: defaultContentSettings,
    });

    loaded.contentSettings.title = 'mutated after assign';

    assert.equal(defaultContentSettings.title, '');
    assert.notEqual(loaded.contentSettings, defaultContentSettings);
});

test('createSlide after loading a product-linked slide starts from empty defaults', () => {
    mergeSlideSettings(productLinkedSlide(), {
        slideSettings: defaultSlideSettings,
        contentSettings: defaultContentSettings,
    });

    const created = applyCreateSlideDefaults({
        id: 'new-slide',
        name: 'New slide',
        active: false,
        activeFrom: null,
        activeUntil: null,
        productId: undefined,
        product: { id: 'stale-product' },
        categoryId: 'stale-category',
        category: { id: 'stale-category' },
        customFields: {},
        translated: { name: null },
        slideSettings: productLinkedSlide().slideSettings,
        contentSettings: productLinkedSlide().contentSettings,
    } as unknown as ElysiumSlide);

    assert.equal(created.slideSettings.slide.linking.type, 'custom');
    assert.equal(created.contentSettings.title, '');
    assert.equal(created.contentSettings.description, '');
    assert.equal(created.contentSettings.button.label, '');
    assert.equal(created.contentSettings.url, '');
    assert.equal(created.contentSettings.focusImageId, null);
    assert.equal(created.contentSettings.slideCover.mobileId, null);
    assert.equal(created.contentSettings.slideCover.tabletId, null);
    assert.equal(created.contentSettings.slideCover.desktopId, null);
    assert.equal(created.contentSettings.slideCover.videoId, null);
    assert.equal(created.productId, null);
    assert.equal(created.categoryId, null);
    assert.equal(created.product, null);
    assert.equal(created.category, null);
    assert.notEqual(created.slideSettings, defaultSlideSettings);
    assert.notEqual(created.contentSettings, defaultContentSettings);
});
