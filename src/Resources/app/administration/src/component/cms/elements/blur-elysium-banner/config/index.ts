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
        selectedSlide: {
            get () {
                return this.element.config.elysiumSlide.value
            },

            set (value) {
                // Note: we are using destructuring assignment syntax here.
                this.element.config.elysiumSlide.value = value
            }
        },

        config () {
            return this.element.config
        },

        viewportConfig () {
            return this.config.viewports.value[this.activeViewport]
        }
    },

    methods: {

        changeViewport (viewport: string) {
            this.activeViewport = viewport
        },
    },

    created() {
        this.initElementConfig('blur-elysium-banner')
    },
})