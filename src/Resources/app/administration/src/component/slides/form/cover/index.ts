import template from './template.html.twig'

const { Component, Mixin } = Shopware 
const { mapMutations, mapState } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('blur-media')
    ],

    inject: [
        'repositoryFactory',
        'acl'
    ],

    computed: {

        ...mapState('blurElysiumSlide', [
            'slide',
            'mediaSidebar',
            'deviceView'
        ]),

        slideViewportSettings () {
            return this.slide.slideSettings.viewports[this.deviceView]
        },

        coverImageUploadTag () {
            const uploadTag = {
                mobile: 'blur-elysium-slide-cover-mobile',
                tablet: 'blur-elysium-slide-cover-tablet',
                desktop: 'blur-elysium-slide-cover'
            }

            return uploadTag[this.deviceView]
        },

        coverImageMedia () {
            if (this.deviceView === 'mobile' && this.slide.slideCoverMobile) {
                return this.slide.slideCoverMobile
            }
            if (this.deviceView === 'tablet' && this.slide.slideCoverTablet) {
                return this.slide.slideCoverTablet
            }
            if (this.deviceView === 'desktop' && this.slide.slideCover) {
                return this.slide.slideCover
            }
                
            return null
        },

        coverImageProperty () {
            if (this.deviceView === 'mobile') {
                return 'slideCoverMobile'
            }
            if (this.deviceView === 'tablet') {
                return 'slideCoverTablet'
            }
            if (this.deviceView === 'desktop') {
                return 'slideCover'
            }
            
            return null
        },

        coverVideoMedia () {
            if (this.slide.slideCoverVideo) {
                return this.slide.slideCoverVideo
            }

            return null
        },

        permissionView() {
            return this.acl.can('blur_elysium_slides.viewer')
        },

        permissionCreate() {
            return this.acl.can('blur_elysium_slides.creator')
        },

        permissionEdit() {
            return this.acl.can('blur_elysium_slides.editor')
        },

        permissionDelete() {
            return this.acl.can('blur_elysium_slides.deleter')
        },
    },

    methods: {

        ...mapMutations('blurElysiumSlide', [
            'setSlideProperty'
        ]),
    },

    watch: {
        'slide.slideCoverMobileId'(value: string | null) {
            if (value !== null) {
                this.fetchMedia(value, 'slideCoverMobile')
            }
        },
        'slide.slideCoverTabletId'(value: string | null) {
            if (value !== null) {
                this.fetchMedia(value, 'slideCoverTablet')
            }
        },
        'slide.slideCoverId'(value: string | null) {
            if (value !== null) {
                this.fetchMedia(value, 'slideCover')
            }
        },
    }
})
