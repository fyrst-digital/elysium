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

    data () {
        return {
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
                    id: 'sm',
                    name: this.$tc('blurElysium.general.small')
                },
                {
                    id: 'md',
                    name: this.$tc('blurElysium.general.medium')
                },
                {
                    id: 'lg',
                    name: this.$tc('blurElysium.general.large')
                }
            ]
        }
    },

    computed: {
        navigationConfig () {
            return this.config.navigation.value
        },
        navigationViewportConfig () {
            return this.config.viewports.value[this.currentViewport].navigation
        }
    }
})