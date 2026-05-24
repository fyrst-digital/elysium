import template from './template.html.twig'
import { previewSchema } from '@elysium/composables/preview-schema'

const { Component, Store } = Shopware;
const { debounce } = Shopware.Utils;

export default Component.wrapComponentConfig({
    template,

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

        window.addEventListener('message', this._handleIframeMessage.bind(this));
    },

    beforeDestroy() {
        window.removeEventListener('message', this._handleIframeMessage.bind(this));
        if (this.iframeAckTimer) {
            clearTimeout(this.iframeAckTimer);
            this.iframeAckTimer = null;
        }
    },

    methods: {
        buildIframeSrc(cacheBuster?: number) {
            this.isLoading = true;
            let src = `${this.baseUrl}/elysium-preview/blur-elysium-slide/${this.slideId}?device=${this.device}&layout=${this.layout}`;
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
            this.sendSlideUpdateImmediate();
            // Wait for storefront JS to confirm values are injected before hiding loader
            this.iframeAckTimer = setTimeout(() => {
                this.isLoading = false;
                this.iframeAckTimer = null;
            }, 300);
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

        sendSlideUpdateImmediate(fields?: string[]) {
            if (this.readOnly) {
                return;
            }

            const iframe = this.$refs.iframe as HTMLIFrameElement | undefined;

            if (!iframe || !iframe.contentWindow) {
                return;
            }

            iframe.contentWindow.postMessage({
                type: 'elysium-slide-update',
                device: this.device,
                slide: JSON.parse(JSON.stringify(this.slide)),
                fields: fields && fields.length > 0 ? fields : ['slide'],
                previewAspectRatio: this.aspectRatioX && this.aspectRatioY ? { x: this.aspectRatioX, y: this.aspectRatioY } : null,
                previewWidth: this.maxWidth,
                framePadding: this.framePadding,
            }, this.baseUrl);
        },

        sendSlideUpdate(fields?: string[]) {
            if (this.readOnly) {
                return;
            }

            if (fields) {
                fields.forEach((f) => this.pendingFields.add(f));
            }
            this._flushSlideUpdate();
        },

        _flushSlideUpdate: debounce(function (this: { pendingFields: Set<string>; sendSlideUpdateImmediate(fields: string[]): void }) {
            const fields = Array.from(this.pendingFields);
            this.pendingFields.clear();
            this.sendSlideUpdateImmediate(fields);
        }, 300),
    },
});
