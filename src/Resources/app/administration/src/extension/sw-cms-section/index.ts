import template from './template.html.twig'

const { Component } = Shopware

export default Component.wrapComponentConfig({
    template,

    methods: {
        onBlockSelection () {
            this.$emit('page-config-open', 'itemConfig');
        }
    }
})