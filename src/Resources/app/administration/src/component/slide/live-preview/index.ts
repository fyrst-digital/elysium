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
        baseUrl: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            iframeSrc: '',
            pendingFields: new Set<string>(),
            isLoading: false,
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

        // --- Direct field changes ---
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
        'slide.contentSettings': {
            deep: true,
            handler() {
                this.sendSlideUpdate(['contentSettings']);
            },
        },
        'slide.slideCover': {
            deep: true,
            handler() {
                this.sendSlideUpdate(['slideCover']);
            },
        },
        'slide.slideCoverMobile': {
            deep: true,
            handler() {
                this.sendSlideUpdate(['slideCoverMobile']);
            },
        },
        'slide.slideCoverTablet': {
            deep: true,
            handler() {
                this.sendSlideUpdate(['slideCoverTablet']);
            },
        },
        'slide.slideCoverVideo': {
            deep: true,
            handler() {
                this.sendSlideUpdate(['slideCoverVideo']);
            },
        },

        // --- Structural slideSettings changes (partials) ---
        'slide.slideSettings.slide.headline.element'() {
            this.sendSlideUpdate(['headlineElement']);
        },
        'slide.slideSettings.slide.linking.type'() {
            this.sendSlideUpdate(['linkingType']);
        },
        'slide.slideSettings.slide.linking.showProductTitle'() {
            this.sendSlideUpdate(['showProductTitle']);
        },
        'slide.slideSettings.slide.linking.showProductDescription'() {
            this.sendSlideUpdate(['showProductDescription']);
        },
        'slide.slideSettings.slide.linking.showProductFocusImage'() {
            this.sendSlideUpdate(['showProductFocusImage']);
        },
        'slide.slideSettings.slide.linking.buttonAppearance'() {
            this.sendSlideUpdate(['buttonAppearance']);
        },
        'slide.slideSettings.slide.linking.buttonSize'() {
            this.sendSlideUpdate(['buttonSize']);
        },
        'slide.slideSettings.slide.linking.overlay'() {
            this.sendSlideUpdate(['linkingOverlay']);
        },
        'slide.slideSettings.slide.linking.openExternal'() {
            this.sendSlideUpdate(['linkingOpenExternal']);
        },
        'slide.slideSettings.slide.cssClass'() {
            this.sendSlideUpdate(['slideCssClass']);
        },

        // --- CSS-only slideSettings changes (styles, no partials) ---
        'slide.slideSettings.slide.bgColor'() {
            this.sendSlideUpdate(['slideBgColor']);
        },
        'slide.slideSettings.slide.bgGradient': {
            deep: true,
            handler() {
                this.sendSlideUpdate(['slideBgGradient']);
            },
        },
        'slide.slideSettings.slide.headline.color'() {
            this.sendSlideUpdate(['headlineColor']);
        },
        'slide.slideSettings.slide.description.color'() {
            this.sendSlideUpdate(['descriptionColor']);
        },
        'slide.slideSettings.container.bgColor'() {
            this.sendSlideUpdate(['containerBgColor']);
        },
        'slide.slideSettings.viewports': {
            deep: true,
            handler() {
                this.sendSlideUpdate(['viewports']);
            },
        },

        device() {
            this.sendSlideUpdate(['device']);
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

        baseUrl() {
            this.buildIframeSrc();
        },
    },

    methods: {
        buildIframeSrc(cacheBuster?: number) {
            this.isLoading = true;
            const adminOrigin = encodeURIComponent(JSON.stringify([window.location.origin]));
            let src = `${this.baseUrl}/elysium-slide/preview/${this.slideId}?device=${this.device}&adminOrigin=${adminOrigin}`;
            if (cacheBuster) {
                src += `&t=${cacheBuster}`;
            }
            this.iframeSrc = src;
        },

        onIframeLoad() {
            this.isLoading = false;
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
                previewAspectRatio: this.aspectRatioX && this.aspectRatioY ? { x: this.aspectRatioX, y: this.aspectRatioY } : null,
                previewWidth: this.maxWidth,
            }, this.baseUrl);
        },

        sendSlideUpdate(fields?: string[]) {
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
