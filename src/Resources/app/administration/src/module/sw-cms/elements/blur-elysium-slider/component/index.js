import template from './blur-cms-el-elysium-slider.twig'
import './blur-cms-el-elysium-slider.scss'

// eslint-disable-next-line no-undef
const { Component, Mixin, Data, Context } = Shopware
const { Criteria } = Data

Component.register('blur-cms-el-elysium-slider', {
    template,

    inject: [
        'repositoryFactory',
        'acl'
    ],

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data () {
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
        }
    },

    computed: {
        selectedSlidesIds: {

            // getter
            get () {
                return this.element.config.elysiumSlideCollection.value
            },
            // setter
            set (newValue) {
                this.element.config.elysiumSlideCollection.value = newValue
            }

        },

        elysiumSlidesRepository () {
            return this.repositoryFactory.create('blur_elysium_slides')
        }
    },

    watch: {
        'element.config.elysiumSlideCollection.value': {
            handler () {
                if (this.selectedSlidesIds.length === 0) {
                    this.selectedSlidesCollection = null
                } else {
                    this.getSlides()
                }
            }
        }
    },

    created () {
        this.createdComponent()
    },

    methods: {
        createdComponent () {
            this.initElementConfig('blur-elysium-slider')
        },

        getSlides () {
            const criteria = new Criteria()
            criteria.setIds(this.selectedSlidesIds)

            this.elysiumSlidesRepository.search(criteria, Context.api).then((res) => {
                this.selectedSlidesCollection = res
                this.isLoading = false
            }).catch((e) => {
                console.warn(e)
            })
        },

        getPreview (property, defaultSnippet) {
            if (this.selectedSlidesCollection?.length > 0 && this.selectedSlidesCollection[this.slideIndex]?.[property]) {
                return this.selectedSlidesCollection[this.slideIndex][property]
            }

            return this.$tc(defaultSnippet)
        },

        getSlideSetting (property) {
            if (this.selectedSlidesCollection?.length > 0 && this.selectedSlidesCollection[this.slideIndex]?.slideSettings?.[property]) {
                return this.selectedSlidesCollection[this.slideIndex].slideSettings[property]
            }

            return null
        },

        getSlideMedia (property) {
            if (this.selectedSlidesCollection?.length > 0 && this.selectedSlidesCollection[this.slideIndex]?.media?.[property]) {
                if (this.selectedSlidesCollection[this.slideIndex].media.thumbnails?.length > 0) {
                    return this.selectedSlidesCollection[this.slideIndex].media.thumbnails.last()[property]
                } else {
                    return this.selectedSlidesCollection[this.slideIndex].media[property]
                }
            }

            return null
        },

        slideArrowClick (iterator) {
            const previewDataLength = parseInt(this.selectedSlidesCollection.length, 10) - 1

            if (parseInt(iterator, 10) === 1 && this.slideIndex < previewDataLength) {
                this.slideIndex += parseInt(iterator, 10)
            } else if (parseInt(iterator, 10) === -1 && this.slideIndex > 0) {
                this.slideIndex += parseInt(iterator, 10)
            }
        }
    }
})
