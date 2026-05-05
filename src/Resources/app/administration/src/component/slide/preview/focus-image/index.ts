import template from './template.html.twig'
import { useViewportProp } from '@elysium/composables/views'

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    inject: ['slide', 'deviceView'],

    data() {
        return {
        };
    },

    watch: {
    },

    computed: {

        focusImageUrl() {
            if (this.slide.presentationMedia) {
                return this.slide.presentationMedia.url || null;
            }
            
            if (
                this.slide.productId &&
                this.slide.slideSettings?.slide?.linking?.type === 'product' &&
                this.slide.slideSettings?.slide?.linking?.showProductFocusImage === true &&
                this.slide.product?.cover?.media
            ) {
                return this.slide.product.cover.media.url || null;
            }


            return null;
        },

        containerStyles() {
            const justifyContent = this.getViewportProp('image.justifyContent')
            const styles = {
                flex: '1 0%',
                display: 'flex',
                flexDirection: 'row',
                justifyContent: justifyContent,
                order: this.getViewportProp('container.order') === 'reverse' ? '1' : '2',
            }
            return styles
        },

        focusImageStyles() {
            const basis = this.getViewportProp('image.basis')
            const styles = {
                width: '100%',
                maxWidth: basis ? `${basis}%` : '100%',
            }
            return styles
        }
    },

    methods: {
        getViewportProp(property: string) {
            return useViewportProp(property, this.deviceView, this.slide.slideSettings.viewports)
        }
    },
});
