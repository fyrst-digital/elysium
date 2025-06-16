import template from './template.html.twig';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    props: {
        color: {
            type: String,
            default: 'currentColor',
        },
        size: {
            type: Number,
            default: 24,
        },
    },
});
