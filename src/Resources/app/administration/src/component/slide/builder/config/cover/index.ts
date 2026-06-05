import template from './template.html.twig';
import { getContentSettingsPlaceholder, getCoverInheritanceSource, getVideoInheritanceSource } from '@elysium/composables/content-settings-display';
import { Media } from '@elysium/types/slide';

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

        elysiumMedia() {
            return Store.get('elysiumMedia');
        },

        slide() {
            return Store.get('elysiumSlide').slide;
        },

        device() {
            return this.elysiumUI.device;
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

        slideCover(): Media | null {
            const mediaId = this.currentCoverMediaId;
            if (!mediaId) return null;
            return this.elysiumMedia.getMedia(mediaId);
        },

        slideCoverVideo(): Media | null {
            const mediaId = this.slide.contentSettings?.slideCover?.videoId ?? null;
            if (!mediaId) return null;
            return this.elysiumMedia.getMedia(mediaId);
        },

        isDefaultLanguage(): boolean {
            const context = Store.get('context');
            return context.api.languageId === context.api.systemLanguageId;
        },

        currentCoverInheritanceSource() {
            return getCoverInheritanceSource(this.slide, this.currentCoverIdKey, this.isDefaultLanguage);
        },

        videoInheritanceSource() {
            return getVideoInheritanceSource(this.slide, this.isDefaultLanguage);
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
        setSlideCover(media: Media, isVideo: boolean = false) {
            const mediaId = media?.id || media?.targetId || null;

            if (mediaId === null) {
                console.error(
                    'mediaId is null. Slide cover media can not be set.'
                );
                return;
            }

            if (!this.slide.contentSettings.slideCover) {
                this.slide.contentSettings.slideCover = {};
            }

            const idKey = isVideo ? 'videoId' : this.currentCoverIdKey;
            this.slide.contentSettings.slideCover[idKey] = mediaId;

            if (media?.path) {
                this.elysiumMedia.setMedia(mediaId, media);
            } else {
                this.mediaRepository
                    .get(mediaId, Context.api)
                    .then((loadedMedia: Media) => {
                        this.elysiumMedia.setMedia(mediaId, loadedMedia);
                    })
                    .catch((exception: Error) => {
                        console.error(exception);
                    });
            }
        },

        removeSlideCover(isVideo: boolean = false) {
            const idKey = isVideo ? 'videoId' : this.currentCoverIdKey;

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

        onAddMediaModal(payload: Media[]) {
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

            const uncachedIds = this.elysiumMedia.getUncachedIds(mediaIds);
            if (uncachedIds.length === 0) return;

            const criteria = new Shopware.Data.Criteria();
            criteria.setIds(uncachedIds);

            this.mediaRepository
                .search(criteria, Context.api)
                .then((result: Media[]) => {
                    result.forEach((media: Media) => {
                        this.elysiumMedia.setMedia(media.id, media);
                    });
                })
                .catch((exception: Error) => {
                    console.error(exception);
                });
        },

        deviceLabelFromKey(key: string | null): string {
            if (!key) return '';
            const map: Record<string, string> = {
                mobileId: 'phone',
                tabletId: 'tablet',
                desktopId: 'desktop',
            };
            return this.$tc('blurElysium.device.' + (map[key] || key));
        },

        contentSettingsPlaceholder(path: string, fallback?: string): string {
            return getContentSettingsPlaceholder(this.slide, path, fallback);
        },
    },

    created() {
        this.viewportsSettings = this.slide.slideSettings.viewports;
        this.loadMediaForCurrentSlide();
    },
});
