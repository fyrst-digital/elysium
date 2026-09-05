import { defaultSlideSettings, defaultContentSettings } from '@elysium/component/slide/settings';
import { ElysiumSlide } from '@elysium/types/slide';

const { Utils } = Shopware;

export function createEmptySlideSettings(): Pick<
    ElysiumSlide,
    'slideSettings' | 'contentSettings'
> {
    return {
        slideSettings: structuredClone(defaultSlideSettings),
        contentSettings: structuredClone(defaultContentSettings),
    };
}

export function applyCreateSlideDefaults(slide: ElysiumSlide): ElysiumSlide {
    Object.assign(slide, createEmptySlideSettings(), {
        productId: null,
        categoryId: null,
        product: null,
        category: null,
    });

    return slide;
}

export function mergeSlideSettings(
    slide: ElysiumSlide,
    properties: Record<string, unknown>
): ElysiumSlide {
    const slideObj = slide as unknown as Record<string, unknown>;

    Object.entries(properties).forEach(([key, defaultSettings]) => {
        const defaults = structuredClone(defaultSettings);

        if (slideObj[key]) {
            slideObj[key] = Utils.object.deepMergeObject(
                defaults as object,
                slideObj[key] as object
            );
        } else {
            slideObj[key] = defaults;
        }
    });

    return slide;
}
