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
        Mixin.getByName('blur-device-utilities')
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
        cmsPageState () {
            return State.get('cmsPageState')
        },

        currentDevice () {

            if (this.cmsPageState.currentCmsDeviceView === 'tablet-landscape') {
                return 'tablet'
            }

            return this.cmsPageState.currentCmsDeviceView
        },

        arrowsConfig () {
            return this.config.arrows.value
        },

        arrowsViewportConfig () {
            return this.config.viewports.value[this.currentDevice].arrows
        }
    },

    methods: {
        cmsDeviceSwitch (device: string) {
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
        this.viewportsSettings = this.config.viewports.value
    }
})