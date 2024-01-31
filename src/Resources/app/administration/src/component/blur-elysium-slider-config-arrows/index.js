// import './blur-elysium-slider-config-navigation.scss'
import template from './blur-elysium-slider-config-arrows.twig'
import { containerBreakpoints } from '@elysiumSlider/utilities/layout'
import { config } from '@elysiumSlider/utilities/identifiers'

export default {
    template,

    props: {
        config: {
            type: Object
        }
    },

    data () {
        return {
            positions: [
                {
                    value: 'in_slider',
                    label: this.$tc('blurElysiumSlider.config.navigation.position.inSlider')
                }
            ],
            icons: [
                {
                    value: 'arrow-head',
                    label: this.$tc('blurElysiumSlider.config.arrows.icons.arrow')
                },
                {
                    value: 'arrow',
                    label: this.$tc('blurElysiumSlider.config.arrows.icons.chevron')
                }
            ],
            sizes: [
                {
                    value: 'sm',
                    label: this.$tc('blurElysiumSlider.general.small')
                },
                {
                    value: 'md',
                    label: this.$tc('blurElysiumSlider.general.medium')
                },
                {
                    value: 'lg',
                    label: this.$tc('blurElysiumSlider.general.large')
                }
            ]
        }
    },

    watch: {
    },

    computed: {
        positionIdentifiers () {
            return config
        },
        containerBreakpoints () {
            return containerBreakpoints
        }

    }
}
