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
        'config.elysiumSlide.value'(value: string) {
            if (value !== '') {
                this.loadPreviewSlide()
            }
        }
    },

    computed: {
        cmsPage() {
            return Store.get('cmsPage');
        },

        slidesRepository () {
            return this.repositoryFactory.create('blur_elysium_slides')
        },

        slideCriteria () {
            const criteria = new Criteria()

            return criteria
        },

        activeViewport () {
            return this.cmsPage.currentCmsDeviceView.split('-')[0]
        },

        config () {
            return this.element?.config ?? null
        },

        aspectRatioStyle () {
            const width = this.config.viewports.value[this.activeViewport].aspectRatio.width
            const height = this.config.viewports.value[this.activeViewport].aspectRatio.height

            if (this.config.viewports.value[this.activeViewport].aspectRatio.auto === true) {
                return 'auto'
            }

            return `${width} / ${height}`
        },

        slideSkeletonTitle () {
            if (this.previewSlide !== null && this.previewSlide.translated?.title) {
                return this.previewSlide.translated.title
            } else if (this.previewSlide !== null) {
                return false
            }
            return this.$tc('blurElysiumBanner.label')
        },

        slideSkeletonDescription () {
            if (this.previewSlide !== null && this.previewSlide.translated?.description) {
                return this.previewSlide.translated.description
            } else if (this.previewSlide !== null) {
                return false
            }
            return this.$tc('blurElysiumBanner.messages.cmsDescription')
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
        this.initElementConfig('blur-elysium-banner')
        
        if (this.config.elysiumSlide?.value !== '') {
            this.loadPreviewSlide()
        }
    },

    methods: {
        loadPreviewSlide () {
            this.slidesRepository.get(this.config.elysiumSlide.value, Context.api, this.slideCriteria).then((result) => {
                this.previewSlide = result
            })
        }
    }
})