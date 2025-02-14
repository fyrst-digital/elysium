import slide from '../slide'
import template from './template.html.twig'

const { Component, Mixin, Store, Context } = Shopware 
const { mapMutations, mapState } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('blur-device-utilities'),
        Mixin.getByName('blur-style-utilities')
    ],

    inject: [
        'repositoryFactory',
        'acl'
    ],

    data () {
        return {
            activeTab: 'coverImage',
            slideCoverMapping: {
                mobile: 'slideCoverMobile',
                tablet: 'slideCoverTablet',
                desktop: 'slideCover'
            },
            mediaLoading: false,
        }
    },

    computed: {

        slide () {
            return Store.get('elysiumSlide').slide
        },

        device () {
            return Store.get('elysiumUI').device
        },

        mediaRepository () {
            return this.repositoryFactory.create('media')
        },

        ...mapState('blurElysiumSlide', [
            // 'slide',
            'mediaSidebar',
        ]),

        slideViewportSettings () {
            return this.slide.slideSettings.viewports[this.device]
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

        slideCoverUploadTag () {
            const uploadTag = {
                mobile: 'blur-elysium-slide-cover-mobile',
                tablet: 'blur-elysium-slide-cover-tablet',
                desktop: 'blur-elysium-slide-cover'
            }

            return uploadTag[this.device]
        },

        slideCover () {
            return this.slide[this.slideCoverMapping[this.device]] ?? null
        },

        slideCoverProp () {
            return this.slideCoverMapping[this.device]
        },

        slideCoverVideo () {
            return this.slide.slideCoverVideo ?? null
        },

        coverVideoUploadElement () {
            return this.$refs.coverVideoUpload ?? null
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

        setSlideCover (media: any, isVideo: boolean = false) {
            this.mediaLoading = true

            let mediaId = media.id || media.targetId || null
            let mediaProp = isVideo ===  true ? 'slideCoverVideo' : this.slideCoverProp

            if (mediaId === null) {
                // throw error message because mediaId is null
                console.error('mediaId is null. Slide cover media can not be set.')
                this.mediaLoading = false
            } else {
                // mediaId is provided handle media assignment
                this.slide[`${mediaProp}Id`] = mediaId

                if (media.path) {

                    // The media already exists in the system no need to fetch it. Use the already existing media object
                    this.slide[mediaProp] = media;
                    this.mediaLoading = false
                } else {

                    // The media does not exist in the system. Fetch the media object from media repository
                    this.mediaRepository.get(
                        mediaId,
                        Context.api
                    ).then((media) => {
                        this.slide[mediaProp] = media;
                        this.mediaLoading = false
                    }).catch((exception) => {
                        console.error(exception)
                        this.mediaLoading = false
                    })
                }
            }
        },

        removeSlideCover (isVideo: boolean = false) {
            let mediaProp = isVideo ===  true ? 'slideCoverVideo' : this.slideCoverProp
            this.slide[`${mediaProp}Id`] = null;
            this.slide[mediaProp] = null;
        },
    },

    created () {
        this.viewportsSettings = this.slide.slideSettings.viewports
    },

    mounted () {
        if (this.$refs.coverVideoUploadElement && this.$refs.coverVideoUploadElement.onClickUpload) {
            const originalClickUpload = this.$refs.coverVideoUploadElement.onClickUpload.bind(this.$refs.coverVideoUploadElement);
            this.$refs.coverVideoUploadElement.onClickUpload = (...args: any[]) => {
                // Your custom logic before
                console.log('onClickUpload was called');
            };
        }
    }
})
