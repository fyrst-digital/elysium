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
            mediaCache: {} as Record<string, MediaItem>,
            mediaModal: {
                open: false,
                type: null as string | null,
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

        focusImageMediaId(): string | null {
            return this.slide.contentSettings?.focusImageId ?? null;
        },

        focusImage(): MediaItem | null {
            const mediaId = this.focusImageMediaId;
            if (!mediaId) return null;
            return this.mediaCache[mediaId] ?? null;
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
                    'mediaId is null. Focus image media can not be set.'
                );
                this.mediaLoading = false;
                return;
            }

            this.slide.contentSettings.focusImageId = mediaId;

            if (media?.path) {
                this.mediaCache[mediaId] = media;
                this.mediaLoading = false;
            } else {
                this.mediaRepository
                    .get(mediaId, Context.api)
                    .then((loadedMedia: MediaItem) => {
                        this.mediaCache[mediaId] = loadedMedia;
                        this.mediaLoading = false;
                    })
                    .catch((exception: Error) => {
                        console.error(exception);
                        this.mediaLoading = false;
                    });
            }
        },

        removeFocusImage() {
            const mediaId = this.slide.contentSettings?.focusImageId;
            if (mediaId && this.mediaCache[mediaId]) {
                delete this.mediaCache[mediaId];
            }
            this.slide.contentSettings.focusImageId = null;
        },

        onAddMediaModal(payload: MediaItem[]) {
            this.setFocusImage(payload[0]);
        },

        closeMediaModal() {
            this.mediaModal.open = false;
        },

        loadMediaForCurrentSlide() {
            const mediaId = this.slide.contentSettings?.focusImageId;
            if (!mediaId) return;

            const criteria = new Shopware.Data.Criteria();
            criteria.setIds([mediaId]);

            this.mediaRepository
                .search(criteria, Context.api)
                .then((result: { items: MediaItem[] }) => {
                    result.items.forEach((media: MediaItem) => {
                        this.mediaCache[media.id] = media;
                    });
                })
                .catch((exception: Error) => {
                    console.error(exception);
                });
        },
    },

    created() {
        this.viewportsSettings = this.slide.slideSettings.viewports;
        this.loadMediaForCurrentSlide();
    },
});
