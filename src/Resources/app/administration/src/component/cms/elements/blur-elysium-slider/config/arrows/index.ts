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

    data () {
        return {
            positions: [
                {
                    value: 'in_slider',
                    label: this.$tc('blurElysiumSlider.config.navigation.position.inSlider')
                }
            ],
            icons: [
                {
                    value: 'arrow-head',
                    label: this.$tc('blurElysiumSlider.config.arrows.icons.chevron')
                },
                {
                    value: 'arrow',
                    label: this.$tc('blurElysiumSlider.config.arrows.icons.arrow')
                }
            ],
            sizes: [
                {
                    value: 'sm',
                    label: this.$tc('blurElysium.general.small')
                },
                {
                    value: 'md',
                    label: this.$tc('blurElysium.general.medium')
                },
                {
                    value: 'lg',
                    label: this.$tc('blurElysium.general.large')
                }
            ]
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

        arrowsConfig () {
            return this.config.arrows.value
        },

        arrowsViewportConfig () {
            return this.config.viewports.value[this.device] ? this.config.viewports.value[this.device].arrows : null
        }
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