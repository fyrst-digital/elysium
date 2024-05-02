import template from './template.html.twig'

const { Component } = Shopware 

export default Component.wrapComponentConfig({
    template,

    props: ['value', 'device'],
    emits: ['update:value'],

    methods: {
        update (value: any) {
            //this.$emit('update', value)
            this.$emit('update:value', value)
        }
    },
})
