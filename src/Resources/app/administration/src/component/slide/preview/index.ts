import template from './template.html.twig'
import { useViewportProp } from '@elysium/composables/views'

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    // inject: ['repositoryFactory'],

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
        showPreviewNotice: {
            type: Boolean,
            default: true,
        }
    },

    data() {
        return {
        };
    },

    watch: {
    },

    computed: {

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

        slideBorderRadius() {
            const property = useViewportProp('slide.borderRadius', this.deviceView, this.slide.slideSettings.viewports)
            return property ? `${property}px` : '0px'
        },

        slideStyles() {
            console.log(this.getViewportProp('slide.borderRadius'))
            const styles = {
                borderRadius: this.getViewportProp('slide.borderRadius') ? `${this.getViewportProp('slide.borderRadius')}px` : '0px',
                backgroundImage: this.slideBgGradient ? this.slideBgGradient : 'none',
                backgroundColor: this.slide.slideSettings?.slide?.bgColor || 'transparent',
                aspectRatio: `${this.aspectRatioX} / ${this.aspectRatioY}`,
                maxHeight: this.maxHeight
            }

            return styles
        }
    },

    methods: {
        getViewportProp(property) {
            return useViewportProp(property, this.deviceView, this.slide.slideSettings.viewports)
        }
    },

    created() {
        console.log('Slide preview created', this.slide, useViewportProp('slide.borderRadius', this.deviceView, this.slide.slideSettings.viewports));
    }
});
