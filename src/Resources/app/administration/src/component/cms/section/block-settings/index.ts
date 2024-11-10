import template from './template.html.twig'

const { Component, State, Mixin } = Shopware

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('blur-device-utilities'),
    ],

    props: ['settings'],

    computed: {
        cmsPage () {
            return State.get('cmsPage')
        },

        currentDevice () {

            if (this.cmsPage.currentCmsDeviceView === 'tablet-landscape') {
                return 'tablet'
            }

            return this.cmsPage.currentCmsDeviceView
        },

        currentViewportSettings () {
            return this.settings.viewports[this.currentDevice]
        }
    },

    methods: {
        cmsDeviceSwitch (device: string) {
            if (this.currentDevice === "desktop") {
                this.cmsPage.setCurrentCmsDeviceView("mobile");
            } else if (this.currentDevice === "mobile") {
                this.cmsPage.setCurrentCmsDeviceView("tablet-landscape");
            } else if (this.currentDevice === "tablet") {
                this.cmsPage.setCurrentCmsDeviceView("desktop");
            }
        },
    },

    watch: {
        settings: {
            handler () {
                this.viewportsSettings = this.settings.viewports
            }
        }
    },

    created () {
        this.viewportsSettings = this.settings.viewports
    }
})