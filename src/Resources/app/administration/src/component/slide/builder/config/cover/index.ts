import template from './template.html.twig';

const { Component, Mixin, Store, Context } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('blur-device-utilities'),
        Mixin.getByName('blur-style-utilities'),
    ],

    inject: ['repositoryFactory', 'acl'],

    data() {
        return {
            slideCoverMapping: {
                mobile: 'slideCoverMobile',
                tablet: 'slideCoverTablet',
                desktop: 'slideCover',
            },
            mediaLoading: false,
            mediaModal: {
                open: false,
                type: null,
            },
        };
    },

    computed: {
        elysiumUI() {
            return Store.get('elysiumUI');
        },

        slide() {
            return Store.get('elysiumSlide').slide;
        },

        device() {
            return this.elysiumUI.device;
        },

        mediaSidebar() {
            return this.elysiumUI.mediaSidebar;
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        slideViewportSettings() {
            return this.slide.slideSettings.viewports[this.device];
        },

        slideCoverUploadTag() {
            const uploadTag = {
                mobile: 'blur-elysium-slide-cover-mobile',
                tablet: 'blur-elysium-slide-cover-tablet',
                desktop: 'blur-elysium-slide-cover',
            };

            return uploadTag[this.device];
        },

        slideCover() {
            return this.slide[this.slideCoverMapping[this.device]] ?? null;
        },

        slideCoverProp() {
            return this.slideCoverMapping[this.device];
        },

        slideCoverVideo() {
            return this.slide.slideCoverVideo ?? null;
        },

        coverVideoUploadElement() {
            return this.$refs.coverVideoUpload ?? null;
        },

        permissionView() {
            return this.acl.can('blur_elysium_slides.viewer');
        },

        permissionCreate() {
            return this.acl.can('blur_elysium_slides.creator');
        },

        permissionEdit() {
            return this.acl.can('blur_elysium_slides.editor');
        },

        permissionDelete() {
            return this.acl.can('blur_elysium_slides.deleter');
        },
    },

    methods: {
        setSlideCover(media: any, isVideo: boolean = false) {
            this.mediaLoading = true;

            const mediaId = media.id || media.targetId || null;
            const mediaProp =
                isVideo === true ? 'slideCoverVideo' : this.slideCoverProp;

            if (mediaId === null) {
                // throw error message because mediaId is null
                console.error(
                    'mediaId is null. Slide cover media can not be set.'
                );
                this.mediaLoading = false;
            } else {
                // mediaId is provided handle media assignment
                this.slide[`${mediaProp}Id`] = mediaId;

                if (media.path) {
                    // The media already exists in the system no need to fetch it. Use the already existing media object
                    this.slide[mediaProp] = media;
                    this.mediaLoading = false;
                } else {
                    // The media does not exist in the system. Fetch the media object from media repository
                    this.mediaRepository
                        .get(mediaId, Context.api)
                        .then((media) => {
                            this.slide[mediaProp] = media;
                            this.mediaLoading = false;
                        })
                        .catch((exception) => {
                            console.error(exception);
                            this.mediaLoading = false;
                        });
                }
            }
        },

        removeSlideCover(isVideo: boolean = false) {
            const mediaProp =
                isVideo === true ? 'slideCoverVideo' : this.slideCoverProp;
            this.slide[`${mediaProp}Id`] = null;
            this.slide[mediaProp] = null;
        },

        openMediaModal(type: string = 'slideCover') {
            this.mediaModal.open = true;
            this.mediaModal.type = type;
        },

        closeMediaModal() {
            this.mediaModal.open = false;
            this.mediaModal.type = null;
        },

        onAddMediaModal(payload) {
            if (payload.length > 0) {
                if (this.mediaModal.type === 'slideCover') {
                    this.setSlideCover(payload[0]);
                } else if (this.mediaModal.type === 'slideCoverVideo') {
                    this.setSlideCover(payload[0], true);
                }
            }
        },
    },

    created() {
        this.viewportsSettings = this.slide.slideSettings.viewports;
    },
});
