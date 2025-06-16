import template from './template.html.twig';

// eslint-disable-next-line no-undef
const { Component, Store } = Shopware;

export default Component.wrapComponentConfig({
    template,

    props: {
        settings: {
            type: Object,
        },
    },

    data() {
        return {
            activeViewport: 'desktop',
        };
    },

    watch: {
        'cmsPage.currentCmsDeviceView'(value) {
            this.activeViewport = value.split('-')[0];
        },
    },

    computed: {
        cmsPage() {
            return Store.get('cmsPage');
        },

        viewports() {
            return Object.keys(this.settings.viewports);
        },
    },

    methods: {
        changeViewport(viewport) {
            let viewportState = viewport;

            this.activeViewport = viewport;

            if (viewportState === 'tablet') {
                viewportState = 'tablet-landscape';
            }

            this.cmsPage.setCurrentCmsDeviceView(viewportState);
        },
    },

    created() {
        this.activeViewport = this.cmsPage.currentCmsDeviceView;
    },
});
