import template from './template.html.twig'

const { Component } = Shopware

export default Component.wrapComponentConfig({
    template,

    computed: {
        styles () {
            const styles: Partial<CSSStyleDeclaration> = {
                gridColumnEnd: 'span 2',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                border: '2px dashed #ccc',
                borderRadius: '4px',
                backgroundColor: '#f9f9f9',
            }

            return styles
        }
    }
})