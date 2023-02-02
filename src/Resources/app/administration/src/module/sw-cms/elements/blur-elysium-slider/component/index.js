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
            slidesData: null,
            inlineBgImage: null,
            inlineColor: '#ffffff',
            slideIndex: 0,
            sliderArrowColor: null,
            sliderDotColor: null,
            sliderDotActiveColor: null,
            selectedSlidesCollection: null
        };
    },

    computed: {
        selectedSlidesIds: {
            
            // getter
            get() {
                return this.element.config.elysiumSlideCollection.value
            },
            // setter
            set(newValue) {
                this.element.config.elysiumSlideCollection.value = newValue
            }
            
        },

        elysiumSlidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        }
    },

    watch: {
        'element.config.elysiumSlideCollection.value': {
            handler() {
                if (this.selectedSlidesIds.length === 0) {
                    this.selectedSlidesCollection = null
                } else {
                    this.getSlides()
                }
            }
        }
    },

    created() {
        this.createdComponent()
    },

    methods: {
        createdComponent() {
            this.initElementConfig( 'blur-elysium-slider' )
        },

        getSlides() {
            const criteria = new Criteria()
            criteria.setIds( this.selectedSlidesIds )

            this.elysiumSlidesRepository.search( criteria, Shopware.Context.api ).then(( res ) => {
                this.selectedSlidesCollection = res
                this.isLoading = false
            }).catch( ( e ) => {
                console.warn( e );
            });
        },

        getPreview( property, defaultSnippet ) {
            return !( this.selectedSlidesCollection === null || this.selectedSlidesCollection === undefined ) && this.selectedSlidesCollection.length > 0 ? this.selectedSlidesCollection[this.slideIndex][property] : this.$tc( defaultSnippet )
        },

        getSlideSetting( property ) {
            return !( this.selectedSlidesCollection === null || this.selectedSlidesCollection === undefined ) ? this.selectedSlidesCollection[this.slideIndex].slideSettings[property] : null
        },

        getSlideMedia( property ) {
            return !( this.selectedSlidesCollection === null || this.selectedSlidesCollection === undefined ) && this.selectedSlidesCollection[this.slideIndex].media ? this.selectedSlidesCollection[this.slideIndex].media[property] : null
        },

        slideArrowClick( iterator ) {
            let previewDataLength = parseInt( this.selectedSlidesCollection.length, 10 ) - 1;

            if ( parseInt( iterator, 10 ) === 1 && this.slideIndex < previewDataLength ) {
                this.slideIndex += parseInt( iterator, 10 );
            } else if ( parseInt( iterator, 10 ) === -1 && this.slideIndex > 0 ) {
                this.slideIndex += parseInt( iterator, 10 );
            }
        },
    }
});