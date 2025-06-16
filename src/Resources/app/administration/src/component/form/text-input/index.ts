import template from './template.html.twig';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    props: {
        // value: Object,
        // device: String,
        // unit: {
        //     type: [String, Boolean],
        //     default: 'Px'
        // },
        // placeholder: {
        //     type: [String, Boolean, Number]
        // }
    },

    emits: ['update-value'],

    methods: {
        update(value) {
            this.$emit('update-value', value);
        },

        // onDeviceNote () {
        //     this.$emit('onDevice', this.device)
        // }
    },
});
