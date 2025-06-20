import template from './template.html.twig';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    props: {
        value: Object,
        showDevice: {
            type: Boolean,
            default: false,
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

    methods: {
        updateValue(value) {
            console.log('updateValue', value);
            this.$emit('update-value', value);
        },

        onDeviceNote() {
            this.$emit('onDevice', this.device);
        },
    },
});
