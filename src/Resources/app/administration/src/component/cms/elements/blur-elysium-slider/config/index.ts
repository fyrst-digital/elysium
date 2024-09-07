import template from './template.html.twig'

const { Component, Mixin } = Shopware

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data() {
        return {
            activeViewport: 'desktop'
        }
    },

    computed: {

        /**
         * @todo since we just looking for at least one slide exist we can not filter orphans at this point
         * move this function to slide-selection component
         */
        selectedSlides: {
            get () {
                return this.element.config.elysiumSlideCollection.value
            },

            set (value) {
                // Note: we are using destructuring assignment syntax here.
                this.element.config.elysiumSlideCollection.value = value
            }
        },
    },

    methods: {

        changeViewport (viewport: string) {
            this.activeViewport = viewport
        },

        /**
         * @deprecated since we just looking for at least one slide exist we can not filter orphans at this point
         * move this function to slide-selection component
         */
        filterOrphans (slides) {
            return this.selectedSlides.filter((selectedSlide) => {
                return slides.find((slide) => {
                    return slide.id === selectedSlide
                })
            })
        },
    },

    created() {
        this.initElementConfig('blur-elysium-slider')
    },
})