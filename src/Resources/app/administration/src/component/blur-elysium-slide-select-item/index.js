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
            allowedImageExtension: [
                'jpg','png','webp','avif','svg'
            ],
            allowedVideoExtension: [
                'mp4','webm'
            ],
            slideData: {},
            itemStyles: {
                backgroundColor: null,
                backgroundImage: null
            }
        };
    },

    watch: {
    },

    computed: {
        slideMedia() {
            if ( this.slideData && this.slideData.media ) {
                return this.slideData.media
            } else if ( this.slideData && this.slideData.mediaPortrait ) {
                return this.slideData.mediaPortrait
            }

            return null
        },

        elysiumSlidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        },

        elysiumSlideCriteria() {
            const criteria = new Criteria()

            return criteria
        },

        itemStyle() {

            if ( this.slideData?.slideSettings?.slideBgColor ) {
                this.itemStyles.backgroundColor = this.slideData.slideSettings.slideBgColor
            }

            if ( this.slideMedia && this.allowedImageExtension.includes( this.slideMedia.fileExtension ) ) {

                if ( this.slideMedia.thumbnails?.length > 0 ) {
                    this.itemStyles.backgroundImage = `url( ${this.slideMedia.thumbnails.last().url} )`
                } else {
                    this.itemStyles.backgroundImage = `url( ${this.slideMedia.url} )`
                }
            }

            return this.itemStyles
        }
    },

    created() {
        this.getElysiumSlide()
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

        editSlide() {
            this.$emit('edit-slide', this.selectedSlide)
        },

        removeSlide() {
            this.$emit('remove-slide', this.selectedSlide)
        },

        startDrag( event ) {
            this.$emit('start-drag', this.selectedSlide, event, this.$refs.selectItem)
        },

        overDrag( event ) {
            this.$emit('over-drag', this.selectedSlide, event, this.$refs.selectItem)
        }
    }
});