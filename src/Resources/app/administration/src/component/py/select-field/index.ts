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
        }
    },

    methods: {
        onOpen(): void {
            this.$nextTick(() => {
                const popover = document.body.querySelector('.mt-select-result-list-popover-wrapper') || null;
                if (popover) {
                    (popover as HTMLElement).classList.add('py-select-popover');
                }
            });
        },
    },
});
