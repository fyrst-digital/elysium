// import './blur-elysium-slider-config-navigation.scss'
import template from './blur-elysium-slider-config-arrows.twig'
import { containerBreakpoints } from '@elysiumSlider/utilities/layout'
import { cmsSliderConfig } from '@elysiumSlider/utilities/identifiers'

export default {
    template,

    props: {
        currentViewport: {
            type: String
        },
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

    computed: {
        positionIdentifiers () {
            return cmsSliderConfig
        },
        containerBreakpoints () {
            return containerBreakpoints
        },
        viewportConfig () {
            return this.config.viewports?.[this.currentViewport] ?? null
        }
    }
}
