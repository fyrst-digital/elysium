// import './blur-elysium-slider-config-navigation.scss'
import template from './blur-elysium-slider-config-sizing.twig'
import { containerBreakpoints } from '@elysiumSlider/utilities/layout'
import { config } from '@elysiumSlider/utilities/identifiers'

export default {
    template,

    props: {
        sizing: {
            type: Array
        }
    },

    data () {
        return {
            selectedViewport: null,
            emptyViewportContainerStyle: {
                backgroundColor: '#f0f2f5',
                padding: '2rem',
                borderRadius: '6px',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                textAlign: 'center',
                flexDirection: 'column'
            }
        }
    },

    computed: {
        positionIdentifiers () {
            return config
        },
        containerBreakpoints () {
            return containerBreakpoints
        },
        selectedSize () {
            if (this.selectedViewport !== null) {
                return this.sizing.find(object => object.viewport === this.selectedViewport)
            }

            return null
        }
    },

    methods: {
        selectViewport (viewport) {
            this.selectedViewport = viewport
        }
    }
}
