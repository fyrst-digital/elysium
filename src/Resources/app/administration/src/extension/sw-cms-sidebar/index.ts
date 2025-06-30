import template from './template.html.twig';

// eslint-disable-next-line no-undef
const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    computed: {
        blurElysiumSectionName() {
            return 'blur-elysium-section';
        },
    },
});