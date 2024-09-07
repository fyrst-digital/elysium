import template from './template.html.twig'

const { Component } = Shopware

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
                    id: 'in_slider',
                    name: this.$tc('blurElysiumSlider.config.navigation.position.inSlider')
                }
            ],
            icons: [
                {
                    id: 'arrow-head',
                    name: this.$tc('blurElysiumSlider.config.arrows.icons.chevron')
                },
                {
                    id: 'arrow',
                    name: this.$tc('blurElysiumSlider.config.arrows.icons.arrow')
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
        arrowsConfig () {
            return this.config.arrows.value
        },
        arrowsViewportConfig () {
            return this.config.viewports.value[this.currentViewport].arrows
        }
    }
})