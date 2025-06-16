import template from './template.html.twig';
import './style.scss';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    props: {
        title: {
            type: [String, Boolean],
            default: false,
        },
        description: {
            type: [String, Boolean],
            default: false,
        },
        config: {
            type: Object,
        },
        badge: {
            type: [Boolean, String],
            default: false,
        },
    },
});
