import template from './template.html.twig'

const { Component, Mixin, Store } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('blur-style-utilities'),
    ],

    data() {
        return {
            styles: {
                container: {
                    mobile: {
                        'position': 'relative',
                        'display': 'flex',
                        'flex-direction': 'column',
                        'padding': '0px',
                        'width': '100%',
                        'overflow-y': 'auto',
                    }
                },
                canvas: {
                    mobile: {
                        'flex': '1 0%',
                        'display': 'flex',
                        'flex-direction': 'row',
                        'flex-wrap': 'wrap',
                        'justify-content': 'center',
                        'align-items': 'center',
                        'margin': '0 auto',
                        'width': '100%',
                        'height': '100%',
                    }
                }
            }
        };
    },

    computed: {

        elysiumSlide() {
            return Store.get('elysiumSlide');
        },

        elysiumUI() {
            return Store.get('elysiumUI');
        },

        slide() {
            return this.elysiumSlide.slide;
        },

        device() {
            return Store.get('elysiumUI').device;
        },

        aspectRatioX() {
            return this.elysiumUI.previewSettings[this.device].aspectRatioX;
        },

        aspectRatioY() {
            return this.elysiumUI.previewSettings[this.device].aspectRatioY;
        },

        width() {
            return this.elysiumUI.previewSettings[this.device].width;
        },
    },

    methods: {
        onAspectRatioXChange(value: number) {
            this.elysiumUI.setPreviewAspectRatioX(this.device, value);
        },

        onAspectRatioYChange(value: number) {
            this.elysiumUI.setPreviewAspectRatioY(this.device, value);
        },

        onWidthChange(value: number | null) {
            this.elysiumUI.setPreviewWidth(this.device, value);
        },
    }
});
