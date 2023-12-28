import template from './template.html.twig';

const { mapState } = Shopware.Component.getComponentHelper();

export default {
    template,

    props: {
        settings: {
            type: Object
        }
    },

    data() {
        return {
            activeViewport: 'desktop'
        }
    },

    watch: {
        currentCmsDeviceView(value) {
            this.activeViewport = value.split('-')[0]
        }
    },

    computed: {
        ...mapState('cmsPageState', [
            'currentCmsDeviceView'
        ]),

        viewports() {
            return Object.keys(this.settings.viewports)
        }
    },

    methods: {
        changeViewport( viewport ) {
            let viewportState = viewport

            this.activeViewport = viewport

            if (viewportState === 'tablet') {
                viewportState = 'tablet-landscape'
            }
            
            Shopware.State.commit('cmsPageState/setCurrentCmsDeviceView', viewportState)
        }
    },

    created() {
        this.activeViewport = this.currentCmsDeviceView
    }
};