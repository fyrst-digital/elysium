import template from './template.html.twig';
import './style.scss';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    computed: {
        styles() {
            const styles: Partial<CSSStyleDeclaration> = {
                // display: 'flex',
                // flexDirection: 'column',
                // alignItems: 'center',
                // justifyContent: 'center',
                // padding: '20px',
                // gap: '10px',
                // border: '2px dashed rgba(0, 0, 0, 0.2)',
                // borderRadius: '4px',
                // backgroundColor: '#ffffff',
                // color: 'var(--color-text-primary-disabled)',
                // cursor: 'pointer',
            };

            return styles;
        },
    },
});
