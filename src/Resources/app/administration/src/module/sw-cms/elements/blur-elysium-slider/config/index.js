import template from './blur-cms-el-config-elysium-slider.twig'
import './blur-cms-el-config-elysium-slider.scss'
import { cmsSliderConfig } from '@elysiumSlider/utilities/identifiers'

// eslint-disable-next-line no-undef
const { Component, Data, Context } = Shopware
const { Criteria } = Data

Component.register('blur-cms-el-config-elysium-slider', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        'cms-element'
    ],

    data () {
        return {
            blurElysiumSlides: null,
            labelProp: 'label',
            selectModel: [],
            activeViewport: 'desktop'
        }
    },

    computed: {
        positionIdentifiers () {
            return cmsSliderConfig
        },
        selectedSlides: {
            get () {
                return this.element.config.elysiumSlideCollection.value
            },

            set (newValue) {
                // Note: we are using destructuring assignment syntax here.
                this.element.config.elysiumSlideCollection.value = newValue
            }
        },

        elysiumSlidesRepository () {
            return this.repositoryFactory.create('blur_elysium_slides')
        },

        defaultCriteria () {
            const defaultCriteria = new Criteria()

            defaultCriteria.addSorting(Criteria.sort(
                'name',
                'ASC',
                true
            ))

            return defaultCriteria
        },

        context () {
            return { ...Context.api }
        }
    },

    created () {
        this.getSlides()
    },

    watch: {
    },

    methods: {

        getSlides () {
            this.elysiumSlidesRepository.search(this.defaultCriteria, Context.api).then((res) => {
                this.blurElysiumSlides = res

                if (res.length === 0) {
                    this.selectedSlides = []
                } else {
                    this.selectedSlides = this.filterOrphans(res)
                }
            }).catch((e) => {
                console.warn(e)
            })
        },

        onChange (content) {
            this.emitChanges(content)
        },

        emitChanges (content) {
            if (content !== this.element.config.elysiumSlideCollection.value) {
                this.element.config.elysiumSlideCollection.value = content
                this.$emit('element-update', this.element)
            }
        },

        onCreateSlide () {
            const route = this.$router.resolve({ name: 'blur.elysium.slides.create' })
            window.open(route.href, '_blank')
        },

        onSlideOverview () {
            const route = this.$router.resolve({ name: 'blur.elysium.slides.index' })
            window.open(route.href, '_blank')
        },

        filterOrphans (slides) {
            return this.selectedSlides.filter((selectedSlide, index) => {
                return slides.find((slide) => {
                    return slide.id === selectedSlide
                })
            })
        },

        onChangeViewport (viewport) {
            this.activeViewport = viewport
        }
    }
})
