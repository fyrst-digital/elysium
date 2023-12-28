import template from './template.html.twig';
import './style.scss';

const { 
    mapState,
    mapMutations,
} = Shopware.Component.getComponentHelper();

Shopware.Component.register( 'sw-cms-block-blur-elysium-block-two-col', {
    template,

    computed: {
        ...mapState('cmsPageState', [
            'currentCmsDeviceView'
        ]),

        settings() {
            if (this.$parent.$props.block?.customFields) {
                return this.$parent.$props.block.customFields
            }

            return null
        },

        dispayColumns() {
            return this.getSettingsByDevice(this.currentCmsDeviceView).columnWrap === true ? 
                '1fr' : 
                `${this.getSettingsByDevice(this.currentCmsDeviceView).width.colOne}fr ${this.getSettingsByDevice(this.currentCmsDeviceView).width.colTwo}fr`
        },

        displayGridGap() {
            if (this.getSettingsByDevice(this.currentCmsDeviceView).gridGap !== "") {
                return this.getSettingsByDevice(this.currentCmsDeviceView).gridGap
            }

            return null
        },

        displayStretch() {
            if (this.settings.columnStretch === true) {
                return 'stretch'
            }

            return 'flex-start'
        }
    },

    methods: {
        getSettingsByDevice( device ) {
            return this.settings.viewports[device.split('-')[0]]
        },
    },
});