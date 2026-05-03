import template from './template.html.twig'

const { Component } = Shopware;
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

    computed: {
        previewUrl(): string {
            return `http://localhost:8000/elysium-slide/preview/${this.slideId}`;
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
        slide: {
            deep: true,
            handler() {
                this.sendSlideUpdate();
            },
        },
        device() {
            this.sendSlideUpdate();
        },
    },

    methods: {
        sendSlideUpdate: debounce(function (this: any) {
            const iframe = this.$refs.iframe as HTMLIFrameElement | undefined;

            if (!iframe || !iframe.contentWindow) {
                return;
            }

            iframe.contentWindow.postMessage({
                type: 'elysium-slide-update',
                device: this.device,
                slide: JSON.parse(JSON.stringify(this.slide)),
            }, 'http://localhost:8000');
        }, 300),
    },
});
