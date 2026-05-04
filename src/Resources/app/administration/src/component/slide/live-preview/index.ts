import template from './template.html.twig'

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
    },

    data() {
        return {
            iframeSrc: '',
            pendingFields: new Set<string>(),
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
                maxWidth: this.maxWidth ? `${this.maxWidth}px` : 'none',
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
            this.buildIframeSrc(counter);
        },
        'slide.title'() {
            this.sendSlideUpdate(['title']);
        },
        'slide.description'() {
            this.sendSlideUpdate(['description']);
        },
        'slide.buttonLabel'() {
            this.sendSlideUpdate(['buttonLabel']);
        },
        'slide.url'() {
            this.sendSlideUpdate(['url']);
        },
        'slide.presentationMedia': {
            deep: true,
            handler() {
                this.sendSlideUpdate(['presentationMedia']);
            },
        },
        'slide.productId'() {
            this.sendSlideUpdate(['productId']);
        },
        'slide.slideSettings': {
            deep: true,
            handler() {
                this.sendSlideUpdate(['slideSettings']);
            },
        },
        'slide.contentSettings': {
            deep: true,
            handler() {
                this.sendSlideUpdate(['contentSettings']);
            },
        },
        device() {
            this.sendSlideUpdate(['device']);
        },
    },

    methods: {
        buildIframeSrc(cacheBuster?: number) {
            const adminOrigin = encodeURIComponent(JSON.stringify([window.location.origin]));
            let src = `http://localhost:8000/elysium-slide/preview/${this.slideId}?device=${this.device}&adminOrigin=${adminOrigin}`;
            if (cacheBuster) {
                src += `&t=${cacheBuster}`;
            }
            this.iframeSrc = src;
        },

        onIframeLoad() {
            this.sendSlideUpdateImmediate();
        },

        sendSlideUpdateImmediate(fields?: string[]) {
            const iframe = this.$refs.iframe as HTMLIFrameElement | undefined;

            if (!iframe || !iframe.contentWindow) {
                return;
            }

            iframe.contentWindow.postMessage({
                type: 'elysium-slide-update',
                device: this.device,
                slide: JSON.parse(JSON.stringify(this.slide)),
                fields: fields && fields.length > 0 ? fields : ['slide'],
            }, 'http://localhost:8000');
        },

        sendSlideUpdate(fields?: string[]) {
            if (fields) {
                fields.forEach((f) => this.pendingFields.add(f));
            }
            this._flushSlideUpdate();
        },

        _flushSlideUpdate: debounce(function (this: any) {
            const fields = Array.from(this.pendingFields);
            this.pendingFields.clear();
            this.sendSlideUpdateImmediate(fields);
        }, 300),
    },
});
