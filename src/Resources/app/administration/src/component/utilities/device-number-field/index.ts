import template from './template.html.twig'

const { Component } = Shopware 

export default Component.wrapComponentConfig({
    template,

    //props: ['value', 'device'],
    props: {
        value: Object,
        device: String,
        // @ts-ignore
        unit: {
            type: [String, Boolean],
            default: 'Px'
        }
    },
    emits: ['update:value', 'onDevice'],

    methods: {
        update (value: any) {
            // @ts-ignore
            this.$emit('update:value', value)
        },

        onDeviceNote () {
            // @ts-ignore
            this.$emit('onDevice', this.device)
        }
    },
})
