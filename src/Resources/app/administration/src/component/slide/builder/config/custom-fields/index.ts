import template from './template.html.twig';

const { Component, Mixin, Store } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('blur-device-utilities'),
    ],

    computed: {
        elysiumSlide() {
            return Store.get('elysiumSlide');
        },

        slide() {
            return this.elysiumSlide.slide;
        },

        customFieldSet() {
            return this.elysiumSlide.customFieldSet;
        },

        hasCustomFields() {
            return this.customFieldSet.total > 0 ? true : false;
        },
    },
});
