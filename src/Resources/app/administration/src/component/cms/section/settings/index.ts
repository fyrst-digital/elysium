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

    created () {
        this.viewportsSettings = this.settings.viewports
        console.log('settings', this.currentViewportSettings)
    }
})