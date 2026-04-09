import template from './template.html.twig';

const { Component, Store, Mixin } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [Mixin.getByName('blur-device-utilities')],

    props: ['settings', 'advanced'],

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
            return this.settings?.viewports[this.device] || null;
        },

        inElysiumSection() {
            const sectionId = this.cmsPage.selectedBlock.sectionId
            const section = this.cmsPage.currentPage?.sections?.find((s) => s.id === sectionId)
            return section.type === 'blur-elysium-section'
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

        getAdvancedError(property: string) {
            return Shopware.Store.get('error').getApiError(
                this.cmsPage.selectedBlock,
                `customFields.elysiumBlockAdvanced.${property}`,
            )
        }
    },

    watch: {
        settings: {
            handler() {
                this.viewportsSettings = this.settings?.viewports || null;
            },
        },
    },

    created() {
        this.viewportsSettings = this.settings?.viewports || null;
    },
});
