import template from './template.html.twig'

const { Component, Mixin, Store } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('blur-style-utilities'),
    ],

    data() {
        return {
            width: null,
            aspectRatioX: 4,
            aspectRatioY: 3, 
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
                        'padding': '40px',
                        'margin': '0 auto',
                        'width': '100%',
                        'max-width': '1120px',
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
    }
});
