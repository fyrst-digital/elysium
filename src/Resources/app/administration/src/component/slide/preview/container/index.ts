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

        containerStyles() {
            const styles = {
                flex: '1 auto',
                display: 'flex',
                flexWrap: 'wrap',
                position: 'relative',
                zIndex: 5,
                flexDirection: this.getViewportProp('container.columnWrap') ? 'column' : 'row',
                justifyContent: this.getViewportProp('container.justifyContent') || 'normal',
                alignItems: this.getViewportProp('container.alignItems') || 'center',
                gap: this.getViewportProp('container.gap') ? `${this.getViewportProp('container.gap')}px` : '20px',
                backgroundColor: this.slide.slideSettings?.container?.bgColor || 'transparent',
                paddingBlock: this.getViewportProp('container.paddingY') ? `${this.getViewportProp('container.paddingY')}px` : '15px',
                paddingInline: this.getViewportProp('container.paddingX') ? `${this.getViewportProp('container.paddingX')}px` : '15px',
                borderRadius: this.getViewportProp('container.borderRadius') ? `${this.getViewportProp('container.borderRadius')}px` : '0px',
                maxWidth: this.getViewportProp('container.maxWidth') ? `${this.getViewportProp('container.maxWidth')}px` : 'none'
            }

            return styles
        }
    },

    methods: {
        getViewportProp(property: string) {
            return useViewportProp(property, this.deviceView, this.slide.slideSettings.viewports)
        }
    },

    created() {
    }
});
