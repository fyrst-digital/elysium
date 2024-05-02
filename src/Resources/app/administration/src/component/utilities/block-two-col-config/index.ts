import template from './template.html.twig'

// eslint-disable-next-line no-undef
const { Component, State } = Shopware
const { mapState } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    props: {
        settings: {
            type: Object
        }
    },

    data () {
        return {
            activeViewport: 'desktop'
        }
    },

    watch: {
        currentCmsDeviceView (value) {
            this.activeViewport = value.split('-')[0]
        }
    },

    computed: {
        ...mapState('cmsPageState', [
            'currentCmsDeviceView'
        ]),

        viewports () {
            return Object.keys(this.settings.viewports)
        }
    },

    methods: {
        changeViewport (viewport: string) {
            let viewportState = viewport

            this.activeViewport = viewport

            if (viewportState === 'tablet') {
                viewportState = 'tablet-landscape'
            }

            State.commit('cmsPageState/setCurrentCmsDeviceView', viewportState)
        }
    },

    created () {
        this.activeViewport = this.currentCmsDeviceView
    }
})
