import template from './template.html.twig'

const { Component, Mixin, Data, Store, Context } = Shopware
const { Criteria } = Data

export default Component.wrapComponentConfig({
    template,

    inject: [
        'repositoryFactory',
        'acl'
    ],

    data() {
        return {
            previewSlide: null
        }
    },

    mixins: [
        Mixin.getByName('cms-element')
    ],

    watch: {
        'config.elysiumSlideCollection.value'(value) {
            if (value.length > 0) {
                this.loadPreviewSlide()
            } else {
                this.previewSlide = null
            }
        }
    },

    computed: {
        cmsPageState () {
            return Store.get('cmsPageState');
        },

        slidesRepository () {
            return this.repositoryFactory.create('blur_elysium_slides')
        },

        slideCriteria () {
            const criteria = new Criteria()

            return criteria
        },

        activeViewport () {
            return this.cmsPageState.currentCmsDeviceView.split('-')[0]
        },

        config () {
            return this.element?.config ?? null
        },

        aspectRatioStyle () {
            const width = this.config.viewports.value[this.activeViewport].sizing.aspectRatio.width
            const height = this.config.viewports.value[this.activeViewport].sizing.aspectRatio.height

            return `${width} / ${height}`
        },

        slideSkeletonTitle () {
            if (this.previewSlide !== null && this.previewSlide.translated?.title) {
                return this.previewSlide.translated.title
            } else if (this.previewSlide !== null) {
                return false
            }
            return this.$tc('blurElysiumSlider.label')
        },

        slideSkeletonDescription () {
            if (this.previewSlide !== null && this.previewSlide.translated?.description) {
                return this.previewSlide.translated.description
            } else if (this.previewSlide !== null) {
                return false
            }
            return this.$tc('blurElysiumSlider.messages.cmsDescription')
        },

        slideSkeletonCover () {
            let defaultCover = null

            if (this.previewSlide !== null) {
                
                if (this.previewSlide?.slideCover) {
                    defaultCover = this.previewSlide.slideCover.url
                }

                if (this.activeViewport === 'mobile' && this.previewSlide?.slideCoverMobile) {
                    return this.previewSlide?.slideCoverMobile.url ?? defaultCover
                }

                if (this.activeViewport === 'tablet' && this.previewSlide?.slideCoverTablet) {
                    return this.previewSlide?.slideCoverTablet.url ?? defaultCover
                }
            }

            return defaultCover
        },

        slideSkeletonStyles () {
            const styles: Partial<CSSStyleDeclaration> = {}

            if (this.previewSlide !== null) {
                styles['--slide-border-color'] = 'transparent'

                if (this.previewSlide.slideSettings?.slide?.bgColor) {
                    styles['--slide-bg-color'] = this.previewSlide.slideSettings.slide.bgColor
                }
            }

            if (this.slideSkeletonCover !== null) {
                styles.backgroundImage = `url(${this.slideSkeletonCover})`
            }

            styles['--slide-aspect-ratio'] = this.aspectRatioStyle

            return styles
        }
    },

    created() {
        this.initElementConfig('blur-elysium-slider')
        
        if (this.config.elysiumSlideCollection?.value?.length > 0) {
            this.loadPreviewSlide()
        }
    },

    methods: {
        loadPreviewSlide () {
            this.slidesRepository.get(this.config.elysiumSlideCollection.value[0], Context.api, this.slideCriteria).then((result) => {
                this.previewSlide = result
            })
        }
    }
})