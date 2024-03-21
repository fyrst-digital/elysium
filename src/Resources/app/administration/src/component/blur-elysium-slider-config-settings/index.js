// import './blur-elysium-slider-config-navigation.scss'
import template from './blur-elysium-slider-config-settings.twig'
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
        viewportConfig () {
            return this.config.viewports?.[this.currentViewport] ?? null
        }
    }
}
