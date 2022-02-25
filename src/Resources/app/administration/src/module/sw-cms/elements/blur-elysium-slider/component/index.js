import template from './blur-cms-el-elysium-slider.twig';
import './blur-cms-el-elysium-slider.scss';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Shopware.Component.register( 'blur-cms-el-elysium-slider', {
    template,

    inject: [
        'repositoryFactory',
        'acl',
    ],

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data() {
        return {
            editable: true,
            isLoading: true,
            slideCollectionIds: null,
            slidesData: null,
            inlineBgImage: null,
            inlineColor: '#ffffff',
            test: 'www.google.de',
            slideIndex: parseInt( 0, 10 ),
            sliderArrowColor: null,
            sliderDotColor: null,
            sliderDotActiveColor: null
        };
    },

    computed: {
        elysiumSlidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        },

        slidesCollectionCriteria() {
            const criteria = new Criteria();

            criteria.setIds( this.slideCollectionIds );

            return criteria;
        },

        previewData: {

            get: function() {
                return this.slidesData;
            },

            set: function( value ) {
                this.slidesData = value;
            }
        },

        mediaUrl() {
            if ( this.previewData[this.slideIndex].media === undefined ) {
                return null;
            }

            return this.previewData[this.slideIndex].media.url;
        },

        previewStyles: {

            get: function() {
                return {
                    color: this.inlineColor,
                    backgroundImage: 'url(' + this.inlineBgImage + ')'
                };
            },

            set: function( value ) {
                this.inlineBgImage = value;
            }
        }
    },

    watch: {
        cmsPageState: {
            deep: true,
            handler() {
                this.updatePreviewData();
            }
        },

        'element.config.headline.source': {
            handler() {
                this.updatePreviewData();
            }
        }
    },

    created() {
        this.createdComponent();
        this.updatePreviewData();
        this.setConfigProperties();
    },

    methods: {
        createdComponent() {
            this.initElementConfig( 'blur-elysium-slider' );
        },

        updatePreviewData() {
            this.slideCollectionIds = this.element.config.elysiumSlideCollection.value;

            if ( this.slideCollectionIds.length > 0  ) {

                this.elysiumSlidesRepository.search(
                    this.slidesCollectionCriteria,
                    Shopware.Context.api
                ).then(( blurElysiumSlidesCollection ) => {
                    if ( blurElysiumSlidesCollection[0].media ) {
                        this.previewStyles = blurElysiumSlidesCollection[0].media.url;
                    } else {
                        this.previewStyles = null;
                    }
                    
                    this.previewData = blurElysiumSlidesCollection;
                });    
            } else {
                this.previewData = null;
            }
        },

        setConfigProperties() {
            if ( this.element.config.sliderArrowColor.value ) {
                this.sliderArrowColor = this.element.config.sliderArrowColor.value;
            }
            if ( this.element.config.sliderDotColor.value ) {
                this.sliderDotColor = this.element.config.sliderDotColor.value;
            }
            if ( this.element.config.sliderDotActiveColor.value ) {
                this.sliderDotActiveColor = this.element.config.sliderDotActiveColor.value;
            }
        },

        slideArrowClick( iterator ) {
            let previewDataLength = parseInt( this.previewData.length, 10 ) - 1;

            if ( parseInt( iterator, 10 ) === 1 && this.slideIndex < previewDataLength ) {
                this.slideIndex += parseInt( iterator, 10 );
            } else if ( parseInt( iterator, 10 ) === -1 && this.slideIndex > 0 ) {
                this.slideIndex += parseInt( iterator, 10 );
            }
        }
    }
});