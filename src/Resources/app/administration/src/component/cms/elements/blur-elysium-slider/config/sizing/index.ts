import template from './template.html.twig'

const { Component, Mixin, State } = Shopware

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

    data () {
        return {
            viewportsSettings: this.config.viewports.value,
        }
    },

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

        sizingConfig () {
            return this.config.sizing.value
        },

        sizingViewportConfig () {
            return this.config.viewports.value[this.currentDevice].sizing
        }
    },

    methods: {
        cmsDeviceSwitch () {
            if (this.currentDevice === "desktop") {
                this.cmsPage.setCurrentCmsDeviceView("mobile");
            } else if (this.currentDevice === "mobile") {
                this.cmsPage.setCurrentCmsDeviceView("tablet-landscape");
            } else if (this.currentDevice === "tablet") {
                this.cmsPage.setCurrentCmsDeviceView("desktop");
            }
        },
    },

    created () {
        this.viewportsSettings = this.config.viewports.value
    }
})