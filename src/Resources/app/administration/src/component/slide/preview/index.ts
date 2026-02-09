import template from './template.html.twig'
import { useViewportProp } from '@elysium/composables/views'

const { Component, Vue } = Shopware;

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
                if (typeof value !== 'string') return false
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

    provide() {
        return {
            slide: this.slide,
            headline: this.headline,
            description: this.description,
            deviceView: Vue.computed(() => this.deviceView),
        };
    },

    data() {
        return {
        };
    },

    watch: {
    },

    computed: {

        headline () {
            return this.slide.title || null
        },

        description () {
            return this.slide.description || null
        },

        slideCoverImage() {
            return null
        },

        slideBgGradient() {
            const allowedGradientTypes = ['linear-gradient', 'radial-gradient']
            const bgGradient = this.slide.slideSettings?.slide?.bgGradient || null;
            const startColor = bgGradient?.startColor || 'transparent'
            const endColor = bgGradient?.endColor || 'transparent'
            const gradientType = allowedGradientTypes.includes(bgGradient?.gradientType) ? bgGradient?.gradientType : null
            const gradientDeg = bgGradient?.gradientDeg || null
            if (startColor && endColor && gradientType && gradientDeg) {
                return `${gradientType}(${gradientDeg}deg, ${startColor}, ${endColor})`
            }
            return null
        },

        slideStyles() {
            const styles = {
                display: 'flex',
                flexWrap: 'wrap',
                flexDirection: 'row',
                position: 'relative',
                alignItems: this.getViewportProp('slide.alignItems') || 'center',
                justifyContent: this.getViewportProp('slide.justifyContent') || 'center',
                paddingInline: this.getViewportProp('slide.paddingY') ? `${this.getViewportProp('slide.paddingY')}px` : '15px',
                paddingBlock: this.getViewportProp('slide.paddingX') ? `${this.getViewportProp('slide.paddingX')}px` : '15px',
                borderRadius: this.getViewportProp('slide.borderRadius') ? `${this.getViewportProp('slide.borderRadius')}px` : '0px',
                backgroundImage: this.slideBgGradient ? this.slideBgGradient : 'none',
                backgroundColor: this.slide.slideSettings?.slide?.bgColor || 'transparent',
                aspectRatio: `${this.aspectRatioX} / ${this.aspectRatioY}`,
                maxHeight: this.maxHeight,
                maxWidth: this.maxWidth ? `${this.maxWidth}px` : 'none',
            }

            return styles
        }
    },

    methods: {
        getViewportProp(property: any) {
            return useViewportProp(property, this.deviceView, this.slide.slideSettings.viewports)
        }
    },

    created() {
    }
});
