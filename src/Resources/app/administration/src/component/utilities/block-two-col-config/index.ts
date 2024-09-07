import template from './template.html.twig'

// eslint-disable-next-line no-undef
const { Component, Store } = Shopware

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
        'cmsPageState.currentCmsDeviceView' (value) {
            this.activeViewport = value.split('-')[0]
        }
    },

    computed: {
        cmsPageState() {
            return Store.get('cmsPageState');
        },

        viewports () {
            return Object.keys(this.settings.viewports)
        }
    },

    methods: {
        changeViewport (viewport) {
            let viewportState = viewport

            this.activeViewport = viewport

            if (viewportState === 'tablet') {
                viewportState = 'tablet-landscape'
            }

            this.cmsPageState.setCurrentCmsDeviceView(viewportState)
        }
    },

    created () {
        this.activeViewport = this.cmsPageState.currentCmsDeviceView
    }
})
