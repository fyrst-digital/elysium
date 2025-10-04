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
                        position: 'sticky',
                        top: '0',
                        zIndex: '10',
                        display: 'flex',
                        alignItems: 'center',
                        gap: '16px',
                        padding: '16px 24px',
                        backgroundColor: '#ffffff',
                        borderBottom: '1px solid #e6e6e6',
                    }
                },
                label: {
                    mobile: {
                        flex: '1 0%',
                        lineHeight: '1.25',
                        fontWeight: '600',
                        fontSize: '14px',
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
