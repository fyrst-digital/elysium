import template from './template.html.twig'
import { useViewportProp } from '@elysium/composables/views'

const { Component, Mixin, Data, Store, Context } = Shopware;
const { Criteria } = Data;

export default Component.wrapComponentConfig({
    template,

    inject: ['repositoryFactory', 'acl'],

    data() {
        return {
            previewSlide: null,
        };
    },

    mixins: [Mixin.getByName('cms-element')],

    watch: {
        'config.elysiumSlideCollection.value'(value) {
            if (value.length > 0) {
                this.loadPreviewSlide();
            } else {
                this.previewSlide = null;
            }
        },
    },

    computed: {
        cmsPage() {
            return Store.get('cmsPage');
        },

        elysiumUI() {
            return Store.get('elysiumUI');
        },

        previewDomain() {
            return this.elysiumUI.previewDomain;
        },

        hasAvailableDomains() {
            return this.elysiumUI.salesChannels.some((channel) => channel.domains && channel.domains.length > 0);
        },

        isLoadingSalesChannels() {
            return this.elysiumUI.isLoadingSalesChannels;
        },

        slidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        },

        slideCriteria() {
            const criteria = new Criteria();
            criteria.addAssociation('product');
            criteria.addAssociation('product.cover');
            criteria.addAssociation('product.cover.media');

            return criteria;
        },

        activeViewport() {
            return this.cmsPage.currentCmsDeviceView.split('-')[0];
        },

        config() {
            return this.element?.config ?? null;
        },

        previewContainerStyles() {
            return {
                width: '100%',
                aspectRatio: `${this.getViewportProp('sizing.aspectRatio.width')} / ${this.getViewportProp('sizing.aspectRatio.height')}`,
                maxHeight: this.getViewportProp('sizing.maxHeight') ? `${this.getViewportProp('sizing.maxHeight')}px` : 'none',
            };
        },
    },

    created() {
        this.initElementConfig('blur-elysium-slider');

        if (this.config.elysiumSlideCollection?.value?.length > 0) {
            this.loadPreviewSlide();
        }
    },

    methods: {
        loadPreviewSlide() {
            this.slidesRepository
                .get(
                    this.config.elysiumSlideCollection.value[0],
                    Context.api,
                    this.slideCriteria
                )
                .then((result) => {
                    this.previewSlide = result;
                });
        },

        getViewportProp(property: string) {
            return useViewportProp(property, this.activeViewport, this.config.viewports.value)
        }
    },
});
