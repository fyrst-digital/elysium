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
        'config.elysiumSlide.value'(value: string) {
            if (value !== '') {
                this.loadPreviewSlide();
            }
        },
    },

    computed: {
        cmsPage() {
            return Store.get('cmsPage');
        },

        slidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        },

        slideCriteria() {
            const criteria = new Criteria();

            return criteria;
        },

        activeViewport() {
            return this.cmsPage.currentCmsDeviceView.split('-')[0];
        },

        config() {
            return this.element?.config ?? null;
        },
    },

    created() {
        this.initElementConfig('blur-elysium-banner');

        if (this.config.elysiumSlide?.value !== '') {
            this.loadPreviewSlide();
        }

        console.log(this.config.viewports.value);
    },

    methods: {
        loadPreviewSlide() {
            this.slidesRepository
                .get(
                    this.config.elysiumSlide.value,
                    Context.api,
                    this.slideCriteria
                )
                .then((result) => {
                    this.previewSlide = result;
                });
        },

        getViewportProp(property: any) {
            return useViewportProp(property, this.activeViewport, this.config.viewports.value)
        }
    },
});
