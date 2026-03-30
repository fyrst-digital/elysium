import template from './template.html.twig';

const { Component, Mixin, Store } = Shopware;

export default Component.wrapComponentConfig({
    template,

    inject: ['feature'],

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

        isTimeControlActive(): boolean {
            return this.feature.isActive('elysium_preview_time_control');
        },
    },

    methods: {
        useError (property: string) {
            return this.error.getApiError(this.slide, property);
        },

        trimSlideName(): void {
            if (this.slide.name) {
                this.slide.name = this.slide.name.trim();
            }
        }
    },

    created() {
        this.viewportsSettings = this.slide.slideSettings.viewports;
    },
});
