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

        value: {
            type: String,
            required: false,
            default: null,
        },
    },

    emits: ['update:value'],

    computed: {
        validatedValue(): string {
            return this.value ?? '';
        },
    },

    methods: {
        update(value: string | null): void {
            this.$emit('update:value', value);
        },
    },
});
