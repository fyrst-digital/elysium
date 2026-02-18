interface SlideState {
    slide: unknown;
    customFieldSet: unknown;
}

export default {
    id: 'elysiumSlide',

    state: (): SlideState => ({
        slide: null,
        customFieldSet: null,
    }),

    getters: {
        slideViewportSettings(state: SlideState): unknown {
            const slide = state.slide as { slideSettings?: { viewports?: unknown } } | null;
            return slide?.slideSettings?.viewports ?? null;
        },
    },

    actions: {
        setSlide(slide: unknown): void {
            this.slide = slide;
        },

        setCustomFieldSet(customFieldSet: unknown): void {
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
