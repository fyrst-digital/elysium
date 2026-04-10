import template from './template.html.twig';

const { Component, Store, Mixin } = Shopware;

export default Component.wrapComponentConfig({
    template,

    inject: ['feature'],

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

        optionsBreakpoints() {
            return [
                { value: 'sm', label: 'sm' },
                { value: 'md', label: 'md' },
                { value: 'lg', label: 'lg' },
                { value: 'xl', label: 'xl' },
                { value: 'xxl', label: 'xxl' },
            ];
        },

        isTimeControlActive(): boolean {
            return this.feature.isActive('elysium_preview_time_control');
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

        getSettingsError(property: string) {
            const section = this.cmsPage.currentPage?.sections?.find(
                (s) => s.id === this.cmsPage.selectedSection?.id
            );
            if (!section) {
                return null;
            }

            return Shopware.Store.get('error').getApiError(
                section,
                `customFields.elysiumSectionSettings.${property}`,
            );
        },
    },

    created() {
        this.viewportsSettings = this.settings.viewports;
    },
});
