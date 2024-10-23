import template from './template.html.twig'

const { Component, State, Mixin } = Shopware

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('blur-device-utilities'),
    ],

    props: ['settings'],

    computed: {
        cmsPageState () {
            return State.get('cmsPageState')
        },

        currentDevice () {

            if (this.cmsPageState.currentCmsDeviceView === 'tablet-landscape') {
                return 'tablet'
            }

            return this.cmsPageState.currentCmsDeviceView
        },

        currentViewportSettings () {
            return this.settings.viewports[this.currentDevice]
        }
    },

    methods: {
        cmsDeviceSwitch (device: string) {
            console.log('device', this.currentDevice)

            if (this.currentDevice === "desktop") {
                this.cmsPageState.setCurrentCmsDeviceView("mobile");
            } else if (this.currentDevice === "mobile") {
                this.cmsPageState.setCurrentCmsDeviceView("tablet-landscape");
            } else if (this.currentDevice === "tablet") {
                this.cmsPageState.setCurrentCmsDeviceView("desktop");
            }
        },
    },

    created () {
        this.viewportsSettings = this.settings.viewports
    }
})