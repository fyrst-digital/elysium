import template from './template.html.twig'

// eslint-disable-next-line no-undef
const { Component } = Shopware
const { mapState } = Component.getComponentHelper()

export default {
    template,

    computed: {
        ...mapState('blurElysiumSlidesDetail', [
            'slide',
            'loading'
        ])
    }
}
