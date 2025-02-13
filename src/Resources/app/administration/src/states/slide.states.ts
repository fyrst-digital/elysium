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

    actions: {
        setSlide(slide: any) {
            this.slide = slide;
        },

        setSlideProp(payload: { key: string, value: any }) {
            this.slide[payload.key] = payload.value;
        },

        clearSlide() {
            this.slide = null;
        },
    },
}