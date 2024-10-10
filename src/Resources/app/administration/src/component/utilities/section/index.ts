import template from './template.html.twig'
import "./style.scss"

const { Component } = Shopware

export default Component.wrapComponentConfig({
    template,

    props: {

        title: {
            type: String
        },

        titleIcon: {
            type: String
        },

        description: {
            type: String
        },

        padding: {  
            type: Boolean,
            default: false
        },

        gap: {  
            type: Boolean,
            default: false
        },

        gapY: {  
            type: Boolean,
            default: false
        },

        gapX: {  
            type: Boolean,
            default: false
        },
    }
})
