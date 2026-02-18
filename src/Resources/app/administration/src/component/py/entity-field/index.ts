import template from './template.html.twig';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    inheritAttrs: false,

    props: {
        label: {
            type: String,
            required: false,
            default: undefined,
        },
        layout: {
            type: String,
            required: false,
            default: 'column',
        },
        multi: {
            type: Boolean,
            required: false,
            default: false,
        }
    },
});
