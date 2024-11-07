import template from './template.html.twig'

const { Component, Mixin } = Shopware 
const { mapMutations, mapState } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('blur-media'),
        Mixin.getByName('blur-device-utilities'),
        Mixin.getByName('blur-style-utilities')
    ],

    inject: [
        'repositoryFactory',
        'acl'
    ],

    data () {
        return {
            activeTab: 'coverImage'
        }
    },

    computed: {

        ...mapState('blurElysiumSlide', [
            'slide',
            'mediaSidebar',
            'currentDevice'
        ]),

        slideViewportSettings () {
            return this.slide.slideSettings.viewports[this.currentDevice]
        },

        tabs () {
            return [
                {
                    label: this.$tc('blurElysiumSlides.forms.slideCoverImage.label'),
                    name: 'coverImage',
                },
                {
                    label: this.$tc('blurElysiumSlides.forms.slideCoverVideo.label'),
                    name: 'coverVideo',
                },
            ]
        },

        coverImageUploadTag () {
            const uploadTag = {
                mobile: 'blur-elysium-slide-cover-mobile',
                tablet: 'blur-elysium-slide-cover-tablet',
                desktop: 'blur-elysium-slide-cover'
            }

            return uploadTag[this.currentDevice]
        },

        coverImageMedia () {
            if (this.currentDevice === 'mobile' && this.slide.slideCoverMobile) {
                return this.slide.slideCoverMobile
            }
            if (this.currentDevice === 'tablet' && this.slide.slideCoverTablet) {
                return this.slide.slideCoverTablet
            }
            if (this.currentDevice === 'desktop' && this.slide.slideCover) {
                return this.slide.slideCover
            }
                
            return null
        },

        coverImageProperty () {
            if (this.currentDevice === 'mobile') {
                return 'slideCoverMobile'
            }
            if (this.currentDevice === 'tablet') {
                return 'slideCoverTablet'
            }
            if (this.currentDevice === 'desktop') {
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
    },

    created () {
        this.viewportsSettings = this.slide.slideSettings.viewports
    }
})
