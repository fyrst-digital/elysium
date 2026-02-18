import { SlideViewports, ElysiumSlide } from '@elysium/types/slide';

interface SlideState {
    slide: ElysiumSlide | null;
    customFieldSet: Entity<'custom_field_set'> | null;
}

export default {
    id: 'elysiumSlide',

    state: (): SlideState => ({
        slide: null,
        customFieldSet: null,
    }),

    getters: {
        slideViewportSettings(state: SlideState): SlideViewports | null {
            return state.slide?.slideSettings?.viewports ?? null;
        },
    },

    actions: {
        setSlide(slide: ElysiumSlide | null): void {
            this.slide = slide;
        },

        setCustomFieldSet(customFieldSet: Entity<'custom_field_set'> | null): void {
            this.customFieldSet = customFieldSet;
        },

        setSlideProp({ key, value }: { key: string; value: unknown }): void {
            if (this.slide) {
                (this.slide as Record<string, unknown>)[key] = value;
            }
        },

        clearSlide(): void {
            this.slide = null;
        },

        clearCustomFieldSet(): void {
            this.customFieldSet = null;
        },
    },
};
