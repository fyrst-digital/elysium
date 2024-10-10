import template from './template.html.twig'

const { Component } = Shopware 

export default Component.wrapComponentConfig({
    template,

    data () {
        return {
            selectionLabel: null
        }
    },

    props: {
        value: Object,
        device: String,
        showDevice: {
            type: Boolean,
            default: false
        },
        placeholder: {
            type: [String, Boolean, Number]
        }
    },

    emits: [
        'update:value', 
        'onDevice'
    ],

    computed: {
        classes () {
            const classes = ['blur-select-input']

            if (this.showDevice) {
                classes.push('has-device-indicator')
            }

            return classes
        }
    },

    methods: {
        update (value) {
            /**
             * @description 
             * This validation is necessary because the 
             * clear button of `mt-select` component returns an empty array.
             * I made a issue on this misbehavior: https://github.com/shopware/meteor/issues/293
             * If its fixed this validation is not needed anymore
             */
            if (Array.isArray(value) && value.length === 0) {
                value = null
            }
            this.$emit('update:value', value)
        },

        onDeviceNote () {
            this.$emit('onDevice', this.device)
        }
    },
})
