import template from './template.html.twig'

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    inject: ['repositoryFactory'],

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
            if (this.slide.slideCoverVideo) {
                return this.slide.slideCoverVideo;
            }

            switch (this.deviceView) {
                case 'mobile':
                    return this.slide.slideCoverMobile || this.slide.slideCoverTablet || this.slide.slideCover || null;
                case 'tablet':
                    return this.slide.slideCoverTablet || this.slide.slideCoverMobile || this.slide.slideCover || null;
                default:
                    return this.slide.slideCover || this.slide.slideCoverTablet || this.slide.slideCoverMobile || null;
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
            return this.slide.title || null;
        },

        headlineStyles() {
            const sizes = { mobile: '20px', tablet: '32px', desktop: '40px' };
            return {
                margin: '0px',
                fontWeight: '600',
                fontSize: sizes[this.deviceView] || '20px',
                color: this.slide.slideSettings?.slide?.headline?.color || '#222',
            };
        },

        description() {
            if (this.slide.slideSettings?.slide?.linking?.type === 'product' && this.slide.product?.description) {
                return this.slide.product.description;
            }
            return this.slide.description || null;
        },

        descriptionStyles() {
            const sizes = { mobile: '16px', tablet: '20px', desktop: '24px' };
            return {
                fontSize: sizes[this.deviceView] || '16px',
                color: this.slide.slideSettings?.slide?.description?.color || '#222',
            };
        },

        showButton() {
            const hasUrl = Boolean(this.slide.url) || Boolean(this.slide.productId);
            return hasUrl && Boolean(this.slide.buttonLabel);
        },
    },
});
