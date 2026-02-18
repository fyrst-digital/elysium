import template from './template.html.twig'

const { Component, Mixin } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('blur-style-utilities'),
    ],

    data() {
        return {
            styles: {
                sidebar: { 
                    mobile: { 
                        'border-left': '1px solid #e6e6e6',
                        'overflow-y': 'auto',
                        'width': 'var(--elysium-sidebar-w, 320px)',
                        'background-color': '#ffffff',
                        'color': 'var(--color-text-primary-default, #333333)',
                    },
                    tablet: { 
                        '--elysium-sidebar-w': '375px',
                    },
                    desktop: { 
                        '--elysium-sidebar-w': '440px',
                    }
                }
            },
        };
    }
});
