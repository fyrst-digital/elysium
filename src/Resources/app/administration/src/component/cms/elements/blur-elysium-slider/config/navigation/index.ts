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
                value: 'below_slider',
                label: this.$tc('blurElysiumSlider.config.navigation.position.belowSlider')
            },
            {
                value: 'in_slider',
                label: this.$tc('blurElysiumSlider.config.navigation.position.inSlider')
            }
            ],
            aligns: [
            {
                value: 'start',
                label: this.$tc('blurElysiumSlider.config.navigation.align.left')
            },
            {
                value: 'center',
                label: this.$tc('blurElysiumSlider.config.navigation.align.center')
            },
            {
                value: 'end',
                label: this.$tc('blurElysiumSlider.config.navigation.align.right')
            }
            ],
            shapes: [
            {
                value: 'circle',
                label: this.$tc('blurElysiumSlider.config.navigation.shape.circle')
            },
            {
                value: 'bar',
                label: this.$tc('blurElysiumSlider.config.navigation.shape.bar')
            }
            ],
            sizes: [
            {
                value: 'sm',
                label: this.$tc('blurElysium.general.sm')
            },
            {
                value: 'md',
                label: this.$tc('blurElysium.general.md')
            },
            {
                value: 'lg',
                label: this.$tc('blurElysium.general.lg')
            }
            ]
        }
    },

    computed: {
        navigationConfig () {
            return this.config.navigation.value
        },
        navigationViewportConfig () {
            return this.config.viewports.value[this.deviceView].navigation
        }
    }
})