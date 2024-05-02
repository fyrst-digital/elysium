import template from './template.html.twig'

const { Component, Mixin, State } = Shopware
const { mapState } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    props: {
        currentViewport: {
            type: String,
            default: 'desktop'
        },
        config: {
            type: Object,
            required: true,
        }
    },

    computed: {
        sizingConfig () {
            return this.config.sizing.value
        },
        sizingViewportConfig () {
            return this.config.viewports.value[this.currentViewport].sizing
        }
    }
})