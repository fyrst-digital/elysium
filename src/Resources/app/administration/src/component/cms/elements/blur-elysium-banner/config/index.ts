import template from './template.html.twig';

const { Component, Mixin, Store } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('cms-state'),
        Mixin.getByName('cms-element'),
        Mixin.getByName('blur-device-utilities'),
    ],

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

        /**
         * @todo since we just looking for at least one slide exist we can not filter orphans at this point
         * move this function to slide-selection component
         */
        selectedSlide: {
            get() {
                return this.element.config.elysiumSlide.value;
            },

            set(value) {
                // Note: we are using destructuring assignment syntax here.
                this.element.config.elysiumSlide.value = value;
            },
        },

        config() {
            return this.element.config;
        },

        viewportConfig() {
            return this.config.viewports.value[this.device] || null;
        },
    },

    methods: {
        changeDevice(device: string) {
            this.cmsPage.setCurrentCmsDeviceView(
                device === 'tablet' ? 'tablet-landscape' : device
            );
        },

        cmsDeviceSwitch() {
            if (this.currentDevice === 'desktop') {
                this.cmsPage.setCurrentCmsDeviceView('mobile');
            } else if (this.currentDevice === 'mobile') {
                this.cmsPage.setCurrentCmsDeviceView('tablet-landscape');
            } else if (this.currentDevice === 'tablet') {
                this.cmsPage.setCurrentCmsDeviceView('desktop');
            }
        },
    },

    created() {
        this.initElementConfig('blur-elysium-banner');
        this.viewportsSettings = this.config.viewports.value;
    },
});
