import template from './template.html.twig'
import { useViewportProp } from '@elysium/composables/views'

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    inject: ['slide', 'deviceView'],

    computed: {

        headlineTag () {
            return this.slide.slideSettings?.slide?.headline?.element || 'div'
        },

        headline() {
            return this.slide.title || null
        },

        headlineStyles() {
            const styles = {
                margin: '0px',
                fontWeight: '600',
                fontSize: this.getViewportProp('headline.fontSize') ? `${this.getViewportProp('headline.fontSize')}px` : '20px',
                color: this.slide.slideSettings?.slide?.headline?.color || '#222',
            }
            return styles
        },

        contentStyles() {
            const maxWidth = this.getViewportProp('content.maxWidth')
            const paddingY = this.getViewportProp('content.paddingY')
            const paddingX = this.getViewportProp('content.paddingX')
            const textAlign = this.getViewportProp('content.textAlign')
            const styles = {
                display: 'flex',
                flexDirection: 'column',
                gap: '12px',
                order: this.getViewportProp('container.order') === 'reverse' ? '2' : '1',
                maxWidth: maxWidth ? `${maxWidth}px` : 'none',
                paddingInline: paddingX ? `${paddingX}px` : '0px',
                paddingBlock: paddingY ? `${paddingY}px` : '0px',
                textAlign: textAlign || 'left',
            }
            return styles
        },

        description() {
            return this.slide.description || null
        },

        descriptionStyles() {
            const styles = {
                fontSize: this.getViewportProp('description.fontSize') ? `${this.getViewportProp('description.fontSize')}px` : '20px',
                color: this.slide.slideSettings?.slide?.description?.color || '#222',
            }
            return styles
        },

        showButton() {
            const isCustom = Boolean(this.slide.url) && this.slide.slideSettings?.slide?.linking?.type === 'custom'
            const isProduct = Boolean(this.slide.productId) && this.slide.slideSettings?.slide?.linking?.type === 'product'
            return (isCustom || isProduct) && this.slide.slideSettings?.slide?.linking?.overlay !== true && Boolean(this.slide.buttonLabel)
        }
    },

    methods: {
        getViewportProp(property: string) {
            return useViewportProp(property, this.deviceView, this.slide.slideSettings.viewports)
        }
    },

    created() {
        console.log('slide', this.slide)
    },
});
