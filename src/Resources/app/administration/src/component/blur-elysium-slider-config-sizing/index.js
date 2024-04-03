// import './blur-elysium-slider-config-navigation.scss'
import template from './blur-elysium-slider-config-sizing.twig'
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

    computed: {
        positionIdentifiers () {
            return cmsSliderConfig
        },
        containerBreakpoints () {
            return containerBreakpoints
        },
        sizingConfig () {
            return this.config.sizing.value
        },
        sizingViewportConfig () {
            return this.config.viewports.value[this.currentViewport].sizing
        }
    }
}
