import template from './template.html.twig';
import './style.scss';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    props: {
        label: {
            type: String,
            required: false,
            default: undefined,
        },

        layout: {
            type: String as () => 'row' | 'column',
            required: false,
            default: undefined,
        },

        required: {
            type: Boolean,
            required: false,
            default: false,
        },

        tooltip: {
            type: String,
            required: false,
            default: undefined,
        },
    },
});
