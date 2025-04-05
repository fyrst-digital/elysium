/**
 * @todo replace any with proper types
 */
interface CMSState {
    elementId: string|null;
    elementConfig: any|null;
    selectedSlides: any[];
}

export default {

    id: 'elysiumCMS',

    state: (): CMSState => ({
        elementId: null,
        elementConfig: null,
        selectedSlides: [],
    }),

    actions: {
        setElementId(elementId: string|null) {
            this.elementId = elementId;
        },

        setElementConfig(elementConfig: any|null) {
            this.elementConfig = elementConfig;
        },

        setSelectedSlides(selectedSlides: any[]) {
            this.selectedSlides = selectedSlides;
        },

        clearSelectedSlides() {
            this.selectedSlides = []
        },

        addSelectedSlide(selectedSlide: any) {
            this.selectedSlides.push(selectedSlide);
        },

        removeSelectedSlide(selectedSlide: any) {
            const index = this.selectedSlides.findIndex(slide => slide.id === selectedSlide.id)
            if (index !== -1) this.selectedSlides.splice(index, 1)
        },

        moveSelectedSlide(slide: any, fromIndex: number, toIndex: number) {
            this.selectedSlides.splice(fromIndex, 1)
            this.selectedSlides.splice(toIndex, 0, slide)
        },
    },
}