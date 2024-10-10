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
            positions: [
                {
                    value: 'in_slider',
                    label: this.$tc('blurElysiumSlider.config.navigation.position.inSlider')
                }
            ],
            icons: [
                {
                    value: 'arrow-head',
                    label: this.$tc('blurElysiumSlider.config.arrows.icons.chevron')
                },
                {
                    value: 'arrow',
                    label: this.$tc('blurElysiumSlider.config.arrows.icons.arrow')
                }
            ],
            sizes: [
                {
                    value: 'sm',
                    label: this.$tc('blurElysium.general.small')
                },
                {
                    value: 'md',
                    label: this.$tc('blurElysium.general.medium')
                },
                {
                    value: 'lg',
                    label: this.$tc('blurElysium.general.large')
                }
            ]
        }
    },

    computed: {
        arrowsConfig () {
            return this.config.arrows.value
        },
        arrowsViewportConfig () {
            return this.config.viewports.value[this.deviceView].arrows
        }
    },
})