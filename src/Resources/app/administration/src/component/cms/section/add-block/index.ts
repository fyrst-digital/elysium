import template from './template.html.twig';
import './style.scss';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    computed: {
        styles() {
            const styles: Partial<CSSStyleDeclaration> = {};

            return styles;
        },
    },
});
