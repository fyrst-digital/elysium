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

        focusImageUrl () {
            return this.slide.presentationMedia?.url || null
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
            const maxWidth = this.getViewportProp('image.maxWidth')
            const styles = {
                width: '100%',
                maxWidth: maxWidth ? `${maxWidth}px` : '100%',
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
