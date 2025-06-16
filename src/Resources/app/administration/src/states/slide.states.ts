/**
 * @todo replace any with proper types
 */

interface SlideState {
    slide: any;
    customFieldSet: any;
}

export default {
    id: 'elysiumSlide',

    state: (): SlideState => ({
        slide: null,
        customFieldSet: null,
    }),

    getters: {
        slideViewportSettings(state: SlideState): any {
            return state.slide?.slideSettings?.viewports ?? null;
        },
    },

    actions: {
        setSlide(slide: any): void {
            this.slide = slide;
        },

        setCustomFieldSet(customFieldSet: any): void {
            this.customFieldSet = customFieldSet;
        },

        setSlideProp({ key, value }: { key: string; value: any }): void {
            if (this.slide) {
                this.slide[key] = value;
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
