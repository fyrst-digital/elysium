import template from './template.html.twig'
import "./style.scss"

const { Component } = Shopware

export default Component.wrapComponentConfig({
    template,

    setup() {

        const startWrapperStyles = <CSSStyleDeclaration>{
            position: 'sticky',
            top: '40px'
        }

        return {
            startWrapperStyles
        }
    },
})
