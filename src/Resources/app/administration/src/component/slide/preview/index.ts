import template from './template.html.twig'
import { getDisplayContentSettings } from '@elysium/composables/content-settings-display'

const { Component, Store } = Shopware;

export default Component.wrapComponentConfig({
    template,

    props: {
        slide: {
            type: Object,
            required: true,
        },
        deviceView: {
            type: String,
            default: 'desktop',
            validator(value) {
                if (typeof value !== 'string') return false;
                return ['desktop', 'tablet', 'mobile'].includes(value);
            }
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
        showPreviewNotice: {
            type: Boolean,
            default: true,
        }
    },

    computed: {
        elysiumMedia() {
            return Store.get('elysiumMedia');
        },

        slideBgGradient() {
            const allowedGradientTypes = ['linear-gradient', 'radial-gradient'];
            const bgGradient = this.slide.slideSettings?.slide?.bgGradient || null;
            const startColor = bgGradient?.startColor || 'transparent';
            const endColor = bgGradient?.endColor || 'transparent';
            const gradientType = allowedGradientTypes.includes(bgGradient?.gradientType) ? bgGradient?.gradientType : null;
            const gradientDeg = bgGradient?.gradientDeg || null;
            if (startColor && endColor && gradientType && gradientDeg) {
                return `${gradientType}(${gradientDeg}deg, ${startColor}, ${endColor})`;
            }
            return null;
        },

        slideStyles() {
            return {
                display: 'flex',
                position: 'relative',
                alignItems: 'center',
                justifyContent: 'center',
                backgroundImage: this.slideBgGradient ? this.slideBgGradient : 'none',
                backgroundColor: this.slide.slideSettings?.slide?.bgColor || 'transparent',
                aspectRatio: `${this.aspectRatioX} / ${this.aspectRatioY}`,
                maxHeight: this.maxHeight,
                maxWidth: this.maxWidth ? `${this.maxWidth}px` : 'none',
                overflow: 'hidden',
            };
        },

        currentMedia() {
            const displaySettings = getDisplayContentSettings(this.slide);
            const contentCover = displaySettings?.slideCover ?? {};

            const videoMedia = this.elysiumMedia.getMedia(contentCover.videoId);
            if (videoMedia) {
                return videoMedia;
            }

            // Mobile-first fallback: device only falls back to smaller viewports
            switch (this.deviceView) {
                case 'mobile':
                    return this.elysiumMedia.getMedia(contentCover.mobileId);
                case 'tablet':
                    return this.elysiumMedia.getMedia(contentCover.tabletId)
                        || this.elysiumMedia.getMedia(contentCover.mobileId);
                default:
                    return this.elysiumMedia.getMedia(contentCover.desktopId)
                        || this.elysiumMedia.getMedia(contentCover.tabletId)
                        || this.elysiumMedia.getMedia(contentCover.mobileId);
            }
        },

        isVideo() {
            return this.currentMedia && this.currentMedia?.mediaType?.name === 'VIDEO';
        },

        coverStyles() {
            return {
                position: 'absolute',
                inset: '0',
                width: '100%',
                height: '100%',
                objectFit: 'cover',
            };
        },

        overlayStyles() {
            return {
                position: 'relative',
                zIndex: 5,
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                justifyContent: 'center',
                gap: '12px',
                padding: '20px',
                textAlign: 'center',
            };
        },

        headline() {
            if (this.slide.slideSettings?.slide?.linking?.type === 'product' && this.slide.product?.name) {
                return this.slide.product.name;
            }
            if (this.slide.slideSettings?.slide?.linking?.type === 'category' && this.slide.category?.name) {
                return this.slide.category.name;
            }
            const displaySettings = getDisplayContentSettings(this.slide);
            return displaySettings.title || null;
        },

        headlineStyles() {
            const sizes = { mobile: '20px', tablet: '32px', desktop: '40px' };
            return {
                margin: '0px',
                fontWeight: '600',
                fontSize: sizes[this.deviceView] || '20px',
                color: this.slide.slideSettings?.slide?.headline?.color || '#222',
                textWrap: 'balance',
            };
        },

        description() {
            if (this.slide.slideSettings?.slide?.linking?.type === 'product' && this.slide.product?.description) {
                return this.slide.product.description;
            }
            if (this.slide.slideSettings?.slide?.linking?.type === 'category' && this.slide.category?.description) {
                return this.slide.category.description;
            }
            const displaySettings = getDisplayContentSettings(this.slide);
            return displaySettings.description || null;
        },

        descriptionStyles() {
            const sizes = { mobile: '16px', tablet: '20px', desktop: '24px' };
            return {
                fontSize: sizes[this.deviceView] || '16px',
                color: this.slide.slideSettings?.slide?.description?.color || '#222',
            };
        },

        showButton() {
            const displaySettings = getDisplayContentSettings(this.slide);
            const hasUrl = Boolean(displaySettings.url) || Boolean(this.slide.productId) || Boolean(this.slide.categoryId);
            return hasUrl && Boolean(displaySettings.button?.label);
        },
    },
});
