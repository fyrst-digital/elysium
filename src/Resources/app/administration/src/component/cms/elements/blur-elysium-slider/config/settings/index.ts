import template from './template.html.twig'

const { Component, Mixin, Store } = Shopware

/** 
 * @todo #120 - https://gitlab.com/BlurCreative/Shopware/Plugins/BlurElysiumSlider/-/issues/120
 * Problem: In every slider config component we pass always the same config object as prop.
 * Solution: Create a state via pinia with the config object and subscribe it in the child component.
 */

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('cms-state'),
        Mixin.getByName('blur-device-utilities'),
        Mixin.getByName('blur-style-utilities')
    ],

    props: {
        config: {
            type: Object,
            required: true,
        }
    },

    computed: {
        cmsPage () {
            return Store.get('cmsPage')
        },

        device () {

            if (this.cmsPage.currentCmsDeviceView === 'tablet-landscape') {
                return 'tablet'
            }

            return this.cmsPage.currentCmsDeviceView
        },

        settingsConfig () {
            return this.config.settings.value
        },

        settingsViewportConfig () {
            return this.config.viewports.value[this.device].settings
        },

    },
    
    methods: {
        cmsDeviceSwitch () {
            if (this.device === "desktop") {
                this.cmsPage.setCurrentCmsDeviceView("mobile");
            } else if (this.device === "mobile") {
                this.cmsPage.setCurrentCmsDeviceView("tablet-landscape");
            } else if (this.device === "tablet") {
                this.cmsPage.setCurrentCmsDeviceView("desktop");
            }
        },
    },
    
    created () {
        this.viewportsSettings = this.config.viewports.value
    }
})