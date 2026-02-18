import template from './template.html.twig';

const { Component, Mixin, Store } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [Mixin.getByName('placeholder')],

    computed: {
        elysiumSlide() {
            return Store.get('elysiumSlide');
        },

        slide() {
            return this.elysiumSlide.slide;
        },
    },
});
