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
            slideCoverMapping: {
                mobile: 'mobileId',
                tablet: 'tabletId',
                desktop: 'desktopId',
            },
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

        slideCoverUploadTag() {
            const uploadTag = {
                mobile: 'blur-elysium-slide-cover-mobile',
                tablet: 'blur-elysium-slide-cover-tablet',
                desktop: 'blur-elysium-slide-cover',
            };

            return uploadTag[this.device];
        },

        currentCoverIdKey(): string {
            return this.slideCoverMapping[this.device];
        },

        currentCoverMediaId(): string | null {
            return this.slide.contentSettings?.slideCover?.[this.currentCoverIdKey] ?? null;
        },

        slideCover(): MediaItem | null {
            const mediaId = this.currentCoverMediaId;
            if (!mediaId) return null;
            return this.mediaCache[mediaId] ?? null;
        },

        slideCoverVideo(): MediaItem | null {
            const mediaId = this.slide.contentSettings?.slideCover?.videoId ?? null;
            if (!mediaId) return null;
            return this.mediaCache[mediaId] ?? null;
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
        setSlideCover(media: MediaItem, isVideo: boolean = false) {
            this.mediaLoading = true;

            const mediaId = media?.id || media?.targetId || null;

            if (mediaId === null) {
                console.error(
                    'mediaId is null. Slide cover media can not be set.'
                );
                this.mediaLoading = false;
                return;
            }

            if (!this.slide.contentSettings.slideCover) {
                this.slide.contentSettings.slideCover = {};
            }

            const idKey = isVideo ? 'videoId' : this.currentCoverIdKey;
            this.slide.contentSettings.slideCover[idKey] = mediaId;

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

        removeSlideCover(isVideo: boolean = false) {
            const idKey = isVideo ? 'videoId' : this.currentCoverIdKey;
            const mediaId = this.slide.contentSettings?.slideCover?.[idKey];

            if (mediaId && this.mediaCache[mediaId]) {
                delete this.mediaCache[mediaId];
            }

            if (this.slide.contentSettings?.slideCover) {
                this.slide.contentSettings.slideCover[idKey] = null;
            }
        },

        openMediaModal(type: string = 'slideCover') {
            this.mediaModal.open = true;
            this.mediaModal.type = type;
        },

        closeMediaModal() {
            this.mediaModal.open = false;
            this.mediaModal.type = null;
        },

        onAddMediaModal(payload: MediaItem[]) {
            if (payload.length > 0) {
                if (this.mediaModal.type === 'slideCover') {
                    this.setSlideCover(payload[0]);
                } else if (this.mediaModal.type === 'slideCoverVideo') {
                    this.setSlideCover(payload[0], true);
                }
            }
        },

        loadMediaForCurrentSlide() {
            const cover = this.slide.contentSettings?.slideCover ?? {};
            const mediaIds = [
                cover.mobileId,
                cover.tabletId,
                cover.desktopId,
                cover.videoId,
            ].filter((id): id is string => id !== null && id !== undefined);

            if (mediaIds.length === 0) return;

            const criteria = new Shopware.Data.Criteria();
            criteria.setIds(mediaIds);

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
