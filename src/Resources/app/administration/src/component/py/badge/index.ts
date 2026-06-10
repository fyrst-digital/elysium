import template from './template.html.twig';
import './style.scss';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    props: {
        icon: {
            type: String,
            required: false,
            default: null,
        },
        text: {
            type: String,
            required: true,
        },
        color: {
            type: String,
            required: false,
            default: 'warning',
        },
    },
});
