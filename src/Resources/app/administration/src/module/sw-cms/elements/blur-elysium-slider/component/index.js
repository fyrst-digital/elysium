import template from './blur-cms-el-elysium-slider.twig';
import './blur-cms-el-elysium-slider.scss';

const { Component, Mixin } = Shopware;

Shopware.Component.register( 'blur-cms-el-elysium-slider', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data() {
        return {
            editable: true,
            previewData: null
        };
    },

    watch: {
        cmsPageState: {
            deep: true,
            handler() {
                // this.updateValue();
            }
        },

        'element.config.headline.source': {
            handler() {
                // this.updateValue();
            }
        }
    },

    created() {
        this.createdComponent();
        this.updatePreviewData();

    },

    methods: {
        createdComponent() {
            this.initElementConfig( 'blur-elysium-slider' );
        },

        updatePreviewData() {
            let slideCollectionIds = this.element.config.elysiumSlideCollection.value;

            if ( slideCollectionIds.length > 0  ) {
                console.dir( slideCollectionIds );
            }

            /**
             * @TODO
             * search in blur_elysium_slides repository for all slide collection IDs
             */
        }
    }
});