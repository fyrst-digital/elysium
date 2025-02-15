import template from './template.html.twig'

const { Component, Mixin, Store } = Shopware 
const { mapGetters } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('blur-device-utilities'),
        Mixin.getByName('blur-style-utilities')
    ],

    computed: {

        slide () {
            return Store.get('elysiumSlide').slide
        },

        device () {
            return Store.get('elysiumUI').device
        },

        ...mapGetters('error', [
            'getApiError'
        ]),

        nameError () {
            return this.getApiError(this.slide, 'name');
        },

        slideViewportSettings () {
            return this.slide.slideSettings.viewports[this.device]
        },
    },

    created () {
        this.viewportsSettings = this.slide.slideSettings.viewports
    }
})
