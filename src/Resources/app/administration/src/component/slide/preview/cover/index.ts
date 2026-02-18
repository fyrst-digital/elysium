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
        currentMedia() {

            if (this.slide.slideCoverVideo) {
                return this.slide.slideCoverVideo
            }

            switch (this.deviceView) {
                case 'desktop':
                    return this.slide.slideCover || this.slide.slideCoverTablet || this.slide.slideCoverMobile || null;
                case 'tablet':
                    return this.slide.slideCoverTablet || this.slide.slideCoverMobile || this.slide.slideCover || null;
                case 'mobile':
                    return this.slide.slideCoverMobile || this.slide.slideCover || null;
                default:
                    return this.slide.slideCover || null;
            }
        },

        coverImageStyles() {
            const styles = {
                position: 'absolute',
                inset: '0',
                width: '100%',
                height: '100%',
                objectFit: this.getViewportProp('coverMedia.objectFit') || 'cover',
                objectPosition: `${this.getViewportProp('coverMedia.objectPosX') || 'center'} ${this.getViewportProp('coverMedia.objectPosY') || 'center'}`,
            }

            return styles
        },

        coverVideoStyles() {
            const styles = {
                position: 'absolute',
                inset: '0',
                width: '100%',
                height: '100%',
                objectFit: this.getViewportProp('coverMedia.objectFit') || 'cover',
                objectPosition: `${this.getViewportProp('coverMedia.objectPosX') || 'center'} ${this.getViewportProp('coverMedia.objectPosY') || 'center'}`,
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
