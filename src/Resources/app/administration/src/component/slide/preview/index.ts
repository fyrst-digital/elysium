import template from './template.html.twig'
import { Media } from '@elysium/types/slide';

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

    data() {
        return {
            mediaCache: {} as Record<string, Media>,
        };
    },

    watch: {
        'slide.contentSettings': {
            handler() {
                this.loadMediaForSlide();
            },
            deep: true,
        },
    },

    created() {
        this.loadMediaForSlide();
    },

    methods: {
        loadMediaForSlide() {
            const contentCover = this.slide?.contentSettings?.slideCover ?? {};
            const mediaIds = [
                contentCover.mobileId,
                contentCover.tabletId,
                contentCover.desktopId,
                contentCover.videoId,
                this.slide?.contentSettings?.focusImageId,
            ].filter((id): id is string => Boolean(id));

            if (mediaIds.length === 0) return;

            const uncachedIds = mediaIds.filter((id) => !this.mediaCache[id]);
            if (uncachedIds.length === 0) return;

            const mediaRepository = this.repositoryFactory.create('media');
            const criteria = new Shopware.Data.Criteria();
            criteria.setIds(uncachedIds);

            mediaRepository
                .search(criteria, Shopware.Context.api)
                .then((result: { items: Media[] }) => {
                    result.items.forEach((media: Media) => {
                        this.mediaCache[media.id] = media;
                    });
                })
                .catch((exception: Error) => {
                    console.error(exception);
                });
        },

        getMediaById(id: string | null | undefined): Media | null {
            if (!id) return null;
            return this.mediaCache[id] ?? null;
        },
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
            const contentCover = this.slide?.contentSettings?.slideCover ?? {};

            const videoMedia = this.getMediaById(contentCover.videoId);
            if (videoMedia) {
                return videoMedia;
            }

            switch (this.deviceView) {
                case 'mobile':
                    return this.getMediaById(contentCover.mobileId)
                        || this.getMediaById(contentCover.tabletId)
                        || this.getMediaById(contentCover.desktopId);
                case 'tablet':
                    return this.getMediaById(contentCover.tabletId)
                        || this.getMediaById(contentCover.mobileId)
                        || this.getMediaById(contentCover.desktopId);
                default:
                    return this.getMediaById(contentCover.desktopId)
                        || this.getMediaById(contentCover.tabletId)
                        || this.getMediaById(contentCover.mobileId);
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
            return this.slide.contentSettings?.title || null;
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
            if (this.slide.slideSettings?.slide?.linking?.type === 'category' && this.slide.category?.description) {
                return this.slide.category.description;
            }
            return this.slide.contentSettings?.description || null;
        },

        descriptionStyles() {
            const sizes = { mobile: '16px', tablet: '20px', desktop: '24px' };
            return {
                fontSize: sizes[this.deviceView] || '16px',
                color: this.slide.slideSettings?.slide?.description?.color || '#222',
            };
        },

        showButton() {
            const hasUrl = Boolean(this.slide.contentSettings?.url) || Boolean(this.slide.productId) || Boolean(this.slide.categoryId);
            return hasUrl && Boolean(this.slide.contentSettings?.button?.label);
        },
    },
});
