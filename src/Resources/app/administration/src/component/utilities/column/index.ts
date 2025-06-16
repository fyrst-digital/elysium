import template from './template.html.twig';

const { Component, Mixin } = Shopware;

interface StyleViewports {
    mobile: Partial<CSSStyleDeclaration>;
    tablet?: Partial<CSSStyleDeclaration>;
    desktop?: Partial<CSSStyleDeclaration>;
}

export default Component.wrapComponentConfig({
    template,

    mixins: [Mixin.getByName('blur-style-utilities')],

    props: {
        padding: {
            type: Boolean,
            default: false,
        },

        cols: {
            type: Number,
            default: 12,
        },

        colsTablet: {
            type: Number,
        },

        colsDesktop: {
            type: Number,
        },
    },

    computed: {
        style() {
            const styles: StyleViewports = {
                mobile: { gridColumnEnd: `span ${this.cols}` },
            };

            if (this.padding) {
                styles.mobile.padding = '24px';
            }

            if (this.colsTablet) {
                styles.tablet = { gridColumnEnd: `span ${this.colsTablet}` };
            }

            if (this.colsDesktop) {
                styles.desktop = { gridColumnEnd: `span ${this.colsDesktop}` };
            }

            return this.viewStyle(styles);
        },
    },
});
