import './style.scss'

import template from './template.html.twig'

const { Component } = Shopware
const { mapState } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    computed: {
        ...mapState ('cmsPageState', [
            'currentCmsDeviceView'
        ]),

        blockSettings () {
            if (this.$attrs?.block?.type === 'blur-elysium-block-two-col') {
                return this.$attrs.block.customFields
            }
    
            return null
        },

        dispayColumns () {
            return this.getSettingsByDevice(this.currentCmsDeviceView).columnWrap === true
                ? '1fr'
                : `${this.getSettingsByDevice(this.currentCmsDeviceView).width.colOne}fr ${this.getSettingsByDevice(this.currentCmsDeviceView).width.colTwo}fr`
        },

        displayGridGap () {
            if (this.getSettingsByDevice(this.currentCmsDeviceView).gridGap) {
                return this.getSettingsByDevice(this.currentCmsDeviceView).gridGap
            }

            return null
        },

        displayStretch () {
            if (this.blockSettings.columnStretch === true) {
                return 'stretch'
            }

            return 'flex-start'
        }
    },

    methods: {
        getSettingsByDevice (device: string) {
            return this.blockSettings.viewports[device.split('-')[0]]
        }
    }
})