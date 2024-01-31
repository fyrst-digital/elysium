// import './blur-elysium-slider-config-navigation.scss'
import template from './blur-elysium-slider-config-settings.twig'
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
