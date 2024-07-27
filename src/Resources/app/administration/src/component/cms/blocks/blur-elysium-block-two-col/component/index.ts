import './style.scss'

import template from './template.html.twig'

const { Component, Store } = Shopware
export default Component.wrapComponentConfig({
    template,

    computed: {
        cmsPageState() {
            return Store.get('cmsPageState');
        },

        activeViewport () {
            return this.cmsPageState.currentCmsDeviceView.split('-')[0]
        },

        blockSettings () {
            if (this.$attrs?.block?.type === 'blur-elysium-block-two-col') {
                return this.$attrs.block.customFields
            }
    
            return null
        },

        dispayColumns () {
            return this.getSettingsByDevice(this.activeViewport).columnWrap === true
                ? '1fr'
                : `${this.getSettingsByDevice(this.activeViewport).width.colOne}fr ${this.getSettingsByDevice(this.activeViewport).width.colTwo}fr`
        },

        displayGridGap () {
            if (this.getSettingsByDevice(this.activeViewport).gridGap) {
                return this.getSettingsByDevice(this.activeViewport).gridGap
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
            return this.blockSettings.viewports[device]
        }
    }
})