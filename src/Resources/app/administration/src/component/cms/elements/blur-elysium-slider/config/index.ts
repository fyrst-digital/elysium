import { provide } from 'vue'
import template from './template.html.twig'

const { Component, Mixin, Store } = Shopware

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('cms-state'),
        Mixin.getByName('cms-element'),
    ],

    provide() {
        return {
            selectedSlidesIds: this.element.config.elysiumSlideCollection.value
        }
    },
    
    data() {
        return {
            activeTab: 'content'
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

        tabs () {
            return [
                {
                    label: this.$tc('blurElysiumSlider.config.contentLabel'),
                    name: 'content',
                },
                {
                    label: this.$tc('blurElysiumSlider.config.settingsLabel'),
                    name: 'settings',
                },
                {
                    label: this.$tc('blurElysiumSlider.config.sizingLabel'),
                    name: 'sizing',
                },
                {
                    label: this.$tc('blurElysiumSlider.config.navigationLabel'),
                    name: 'navigation',
                },
                {
                    label: this.$tc('blurElysiumSlider.config.arrowsLabel'),
                    name: 'arrows',
                }
            ]
        },
    },

    methods: {

        changeDevice (device: string) {
            this.cmsPage.setCurrentCmsDeviceView(device === 'tablet' ? 'tablet-landscape' : device)
        },
    },

    created() {
        this.initElementConfig('blur-elysium-slider')
    },
})