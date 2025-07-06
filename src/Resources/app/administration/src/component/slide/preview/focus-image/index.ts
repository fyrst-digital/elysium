import container from '../container';
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
            const styles = {
                flex: '1 0%',
                order: this.getViewportProp('container.order') === 'reverse' ? '1' : '2',
            }
            return styles
        },

        focusImageStyles() {
            const styles = {
                maxWidth: '100%',
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
        console.log('fdsf', this.getViewportProp('container.order'))
    }
});
