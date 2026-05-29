import template from './template.html.twig'
import { previewSchema } from '@elysium/composables/preview-schema'
import { getDisplayContentSettings } from '@elysium/composables/content-settings-display'
import { Media } from '@elysium/types/slide';

const { Component, Store, Data, Context } = Shopware;
const { debounce } = Shopware.Utils;
const { Criteria } = Data;

export default Component.wrapComponentConfig({
    template,

    inject: ['repositoryFactory'],

    props: {
        slideId: {
            type: String,
            required: true,
        },
        slide: {
            type: Object,
            required: true,
        },
        device: {
            type: String,
            default: 'desktop',
        },
        aspectRatioX: {
            type: Number,
            default: 16,
        },
        aspectRatioY: {
            type: Number,
            default: 9,
        },
        maxHeight: {
            type: String,
            default: 'none',
        },
        maxWidth: {
            type: [Number, null],
            default: null,
        },
        baseUrl: {
            type: String,
            required: true,
        },
        readOnly: {
            type: Boolean,
            default: false,
        },
        framePadding: {
            type: Number,
            default: 0,
        },
        layout: {
            type: String,
            default: 'detail',
        },
    },

    data() {
        return {
            iframeSrc: '',
            pendingFields: new Set<string>(),
            isLoading: false,
            iframeAckTimer: null as ReturnType<typeof setTimeout> | null,
        };
    },

    computed: {
        elysiumSlide() {
            return Store.get('elysiumSlide');
        },

        elysiumMedia() {
            return Store.get('elysiumMedia');
        },

        previewStyles(): Record<string, string> {
            return {
                display: 'flex',
                width: '100%',
                height: '100%',
                maxHeight: this.maxHeight,
                overflow: 'hidden',
            };
        },
    },

    watch: {
        slideId: {
            immediate: true,
            handler() {
                this.buildIframeSrc();
            },
        },
        'elysiumSlide.refreshPreviewCounter'(counter) {
            if (this.readOnly) {
                return;
            }
            this.buildIframeSrc(counter);
        },
        device() {
            if (this.readOnly) {
                this.buildIframeSrc();
            } else {
                this.sendSlideUpdate(['device']);
            }
        },
        aspectRatioX() {
            this.sendSlideUpdate(['previewSizing']);
        },
        aspectRatioY() {
            this.sendSlideUpdate(['previewSizing']);
        },
        maxWidth() {
            this.sendSlideUpdate(['previewSizing']);
        },
        framePadding() {
            this.sendSlideUpdate(['previewSizing']);
        },
        baseUrl() {
            this.buildIframeSrc();
        },
    },

    created() {
        // Dynamically register watchers from the preview schema
        previewSchema.fieldMappings.forEach((mapping) => {
            this.$watch(mapping.path, () => {
                this.sendSlideUpdate(mapping.fields);
            }, { deep: mapping.deep ?? false });
        });

        // Watch media ID paths — load media into shared store, then trigger update
        const mediaIdPaths = [
            'slide.contentSettings.slideCover.mobileId',
            'slide.contentSettings.slideCover.tabletId',
            'slide.contentSettings.slideCover.desktopId',
            'slide.contentSettings.slideCover.videoId',
            'slide.contentSettings.focusImageId',
        ];
        mediaIdPaths.forEach((path) => {
            this.$watch(path, () => {
                this.loadMediaForSlide().then(() => {
                    this.sendSlideUpdate(['contentSettings']);
                });
            });
        });

        this.loadMediaForSlide();

        this._boundHandleIframeMessage = this._handleIframeMessage.bind(this);
        window.addEventListener('message', this._boundHandleIframeMessage);
    },

    beforeDestroy() {
        window.removeEventListener('message', this._boundHandleIframeMessage);
        if (this.iframeAckTimer) {
            clearTimeout(this.iframeAckTimer);
            this.iframeAckTimer = null;
        }
    },

    methods: {
        loadMediaForSlide(): Promise<void> {
            const contentCover = this.slide?.contentSettings?.slideCover ?? {};
            const mediaIds = [
                contentCover.mobileId,
                contentCover.tabletId,
                contentCover.desktopId,
                contentCover.videoId,
                this.slide?.contentSettings?.focusImageId,
            ].filter((id): id is string => Boolean(id));

            if (mediaIds.length === 0) return Promise.resolve();

            const uncachedIds = this.elysiumMedia.getUncachedIds(mediaIds);
            if (uncachedIds.length === 0) return Promise.resolve();

            const mediaRepository = this.repositoryFactory?.create('media');
            if (!mediaRepository) return Promise.resolve();

            const criteria = new Criteria();
            criteria.setIds(uncachedIds);

            return mediaRepository
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

        buildIframeSrc(cacheBuster?: number) {
            this.isLoading = true;
            const isNew = this.slide?._isNew === true;
            let src = `${this.baseUrl}/elysium-preview/blur-elysium-slide/${this.slideId}?device=${this.device}&layout=${this.layout}`;
            if (isNew) {
                src += '&new=1';
            }
            if (cacheBuster) {
                src += `&t=${cacheBuster}`;
            }
            this.iframeSrc = src;
        },

        onIframeLoad() {
            if (this.readOnly) {
                this.isLoading = false;
                return;
            }
            // Load media first, then send the initial update with resolved media
            this.loadMediaForSlide().then(() => {
                this.sendSlideUpdate(undefined, true);
                this.iframeAckTimer = setTimeout(() => {
                    this.isLoading = false;
                    this.iframeAckTimer = null;
                }, 300);
            });
        },

        _handleIframeMessage(event: MessageEvent) {
            const iframe = this.$refs.iframe as HTMLIFrameElement | undefined;
            if (!iframe || event.source !== iframe.contentWindow) {
                return;
            }
            if (event.data?.type === 'elysium-slide-update-ack') {
                this.isLoading = false;
                if (this.iframeAckTimer) {
                    clearTimeout(this.iframeAckTimer);
                    this.iframeAckTimer = null;
                }
            }
        },

        /**
         * Sends a slide update to the live preview iframe.
         *
         * @param fields - Which fields changed (for the iframe to selectively update)
                 * @param immediate - If true, sends immediately. If false, debounces (300ms).
         */
        sendSlideUpdate(fields?: string[], immediate: boolean = false) {
            if (this.readOnly) {
                return;
            }

            if (!immediate) {
                if (fields) {
                    fields.forEach((f) => this.pendingFields.add(f));
                }
                this._flushSlideUpdate();
                return;
            }

            const iframe = this.$refs.iframe as HTMLIFrameElement | undefined;

            if (!iframe || !iframe.contentWindow) {
                return;
            }

            // Build a display copy with merged fallback values for the preview iframe
            const displaySlide = JSON.parse(JSON.stringify(this.slide));
            const displaySettings = JSON.parse(JSON.stringify(getDisplayContentSettings(this.slide)));

            // Override media IDs with raw current-language values (no fallback for media).
            // If the user removes an image in the current language, the preview should
            // reflect that removal, not fall back to the default language's image.
            const rawSettings = this.slide.contentSettings ?? {};
            displaySettings.slideCover = JSON.parse(JSON.stringify(rawSettings.slideCover ?? {}));
            displaySettings.focusImageId = rawSettings.focusImageId ?? null;

            displaySlide.contentSettings = displaySettings;

            iframe.contentWindow.postMessage({
                type: 'elysium-slide-update',
                device: this.device,
                slide: displaySlide,
                resolvedMedia: JSON.parse(JSON.stringify(this.elysiumMedia.getResolvedMedia(displaySlide.contentSettings))),
                fields: fields && fields.length > 0 ? fields : ['slide'],
                previewAspectRatio: this.aspectRatioX && this.aspectRatioY ? { x: this.aspectRatioX, y: this.aspectRatioY } : null,
                previewWidth: this.maxWidth,
                framePadding: this.framePadding,
            }, this.baseUrl);
        },

        _flushSlideUpdate: debounce(function (this: { pendingFields: Set<string>; sendSlideUpdate(fields: string[], immediate: boolean): void }) {
            const fields = Array.from(this.pendingFields);
            this.pendingFields.clear();
            this.sendSlideUpdate(fields, true);
        }, 300),
    },
});
