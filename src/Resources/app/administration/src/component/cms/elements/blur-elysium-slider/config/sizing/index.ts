import template from './template.html.twig'

const { Component, Mixin } = Shopware

/** 
 * @todo #120 - https://gitlab.com/BlurCreative/Shopware/Plugins/BlurElysiumSlider/-/issues/120
 * Problem: In every slider config component we pass always the same config object as prop.
 * Solution: Create a state via pinia with the config object and subscribe it in the child component.
 */

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('blur-device-utilities')
    ],

    props: {
        config: {
            type: Object,
            required: true,
        }
    },

    data () {
        return {
            viewportsSettings: this.config.viewports.value,
        }
    },

    computed: {
        sizingConfig () {
            return this.config.sizing.value
        },
        sizingViewportConfig () {
            return this.config.viewports.value[this.deviceView].sizing
        }
    }
})