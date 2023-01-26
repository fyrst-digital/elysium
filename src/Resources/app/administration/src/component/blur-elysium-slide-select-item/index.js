import template from './blur-elysium-slide-select-item.twig';
import './blur-elysium-slide-select-item.scss';

const { Criteria } = Shopware.Data;
const { Component, Context, Mixin } = Shopware;
const { mapPropertyErrors, mapState } = Shopware.Component.getComponentHelper();

Component.register( 'blur-elysium-slide-select-item', {
    template,

    inject: [
        'repositoryFactory'
    ],

    props: {
        selectedSlide: {
            type: String
        }
    },

    data() {
        return {
            isLoading: true,
            slideData: {}
        };
    },

    watch: {
    },

    computed: {
        slideImage() {
            if ( this.slideData && this.slideData.media ) {
                return this.slideData.media.url
            } else if ( this.slideData && this.slideData.mediaPortrait ) {
                return this.slideData.mediaPortrait.url
            }

            return null
        },

        elysiumSlidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        },

        elysiumSlideCriteria() {
            const criteria = new Criteria()

            return criteria
        }
    },

    created() {
        this.getElysiumSlide()
        console.log(this)
    },

    methods: {
        getElysiumSlide() {
            this.elysiumSlidesRepository.get( this.selectedSlide, Shopware.Context.api, this.elysiumSlideCriteria ).then((result) => {
                this.slideData = result
                this.isLoading = false
            })
        },

        positionUp() {
            this.$emit('position-up', this.selectedSlide)
        },

        positionDown() {
            this.$emit('position-down', this.selectedSlide)
        },

        removeSlide() {
            this.$emit('remove-slide', this.selectedSlide)
        },
    }
});