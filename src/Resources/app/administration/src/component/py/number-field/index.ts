import template from './template.html.twig';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    inheritAttrs: false,

    props: {
        label: {
            type: [String, Boolean],
            required: false,
            default: undefined,
        },

        layout: {
            type: String as () => 'row' | 'column',
            required: false,
            default: 'column',
        },
    },
});
