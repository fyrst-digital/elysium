import template from './template.html.twig';

const { Component, Mixin, Store } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('blur-device-utilities'),
        Mixin.getByName('blur-style-utilities'),
    ],

    computed: {
        slide() {
            return Store.get('elysiumSlide').slide;
        },

        device() {
            return Store.get('elysiumUI').device;
        },

        error() {
            return Store.get('error');
        },

        slideViewportSettings() {
            return this.slide.slideSettings.viewports[this.device];
        },
    },

    methods: {
        useError (property: string) {
            return this.error.getApiError(this.slide, property);
        }
    },

    created() {
        this.viewportsSettings = this.slide.slideSettings.viewports;
    },
});
