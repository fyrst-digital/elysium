import template from './template.html.twig';

const { Component, Store, Mixin } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [Mixin.getByName('blur-device-utilities')],

    props: ['settings'],

    computed: {
        cmsPage() {
            return Store.get('cmsPage');
        },

        device() {
            if (this.cmsPage.currentCmsDeviceView === 'tablet-landscape') {
                return 'tablet';
            }

            return this.cmsPage.currentCmsDeviceView;
        },

        currentViewportSettings() {
            return this.settings.viewports[this.device];
        },
    },

    methods: {
        cmsDeviceSwitch() {
            if (this.device === 'desktop') {
                this.cmsPage.setCurrentCmsDeviceView('mobile');
            } else if (this.device === 'mobile') {
                this.cmsPage.setCurrentCmsDeviceView('tablet-landscape');
            } else if (this.device === 'tablet') {
                this.cmsPage.setCurrentCmsDeviceView('desktop');
            }
        },
    },

    watch: {
        settings: {
            handler() {
                this.viewportsSettings = this.settings.viewports;
            },
        },
    },

    created() {
        this.viewportsSettings = this.settings.viewports;
    },
});
