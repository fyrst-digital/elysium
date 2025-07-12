import template from './template.html.twig'
import { useViewportProp } from '@elysium/composables/views'

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    inject: ['slide', 'deviceView', 'headline', 'description'],

    data() {
        return {
        };
    },

    watch: {
    },

    computed: {

        headlineTag () {
            return this.slide.slideSettings?.slide?.headline?.element || 'div'
        },

        contentStyles() {
            const styles = {
                display: 'flex',
                flexDirection: 'column',
                gap: '12px',
                order: this.getViewportProp('container.order') === 'reverse' ? '2' : '1',
            }
            return styles
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

        descriptionStyles() {
            const styles = {
                fontSize: this.getViewportProp('description.fontSize') ? `${this.getViewportProp('description.fontSize')}px` : '20px',
                color: this.slide.slideSettings?.slide?.description?.color || '#222',
            }
            return styles
        }
    },

    methods: {
        getViewportProp(property: any) {
            return useViewportProp(property, this.deviceView, this.slide.slideSettings.viewports)
        }
    },
});
