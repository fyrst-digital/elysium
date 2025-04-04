import template from './template.html.twig'

const { Component, Mixin, Store } = Shopware 

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('blur-device-utilities'),
        Mixin.getByName('blur-style-utilities'),
    ],

    computed: {

        elysiumSlide () {
            return Store.get('elysiumSlide')
        },

        elysiumUI () {
            return Store.get('elysiumUI')
        },

        slide () {
            return this.elysiumSlide.slide
        },

        device () {
            return this.elysiumUI.device
        },

        slideViewportSettings () {
            return this.slide.slideSettings.viewports[this.device]
        },
    },

    created () {
        this.viewportsSettings = this.slide.slideSettings.viewports
    }
})
