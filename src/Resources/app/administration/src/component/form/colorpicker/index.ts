import template from './template.html.twig';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    props: {
        value: {
            type: String,
        },
        device: String,
        unit: {
            type: [String, Boolean],
            default: 'Px',
        },
        placeholder: {
            type: [String, Boolean, Number],
        },
    },

    emits: ['update:value', 'onDevice'],

    computed: {
        validatedValue() {
            if (this.value === null) {
                return '';
            }
            return this.value;
        },
    },

    methods: {
        update(value) {
            this.$emit('update:value', value);
        },
    },
});
