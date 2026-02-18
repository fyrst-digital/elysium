import template from './template.html.twig';

import { MediaItem } from '@elysium/types/media';

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

        focusImage() {
            if (this.slide.presentationMedia) {
                return this.slide.presentationMedia;
            }

            return this.slide.presentationMedia || null;
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
        setFocusImage(media: MediaItem) {
            this.mediaLoading = true;

            const mediaId = media?.id || media?.targetId || null;

            if (mediaId === null) {
                console.error(
                    'mediaId is null. Slide cover media can not be set.'
                );
                this.mediaLoading = false;
            } else {
                this.slide.presentationMediaId = mediaId;

                if (media?.path) {
                    this.slide.presentationMedia = media;
                    this.mediaLoading = false;
                } else {
                    this.mediaRepository
                        .get(mediaId, Context.api)
                        .then((media) => {
                            this.slide.presentationMedia = media;
                            this.mediaLoading = false;
                        })
                        .catch((exception) => {
                            console.error(exception);
                            this.mediaLoading = false;
                        });
                }
            }
        },

        removeFocusImage() {
            this.slide.presentationMediaId = null;
            this.slide.presentationMedia = null;
        },

        onAddMediaModal(payload) {
            this.setFocusImage(payload[0]);
        },

        closeMediaModal() {
            this.mediaModal.open = false;
        },
    },

    created() {
        this.viewportsSettings = this.slide.slideSettings.viewports;
    },
});
