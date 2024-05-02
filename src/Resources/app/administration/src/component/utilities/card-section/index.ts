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

        last: {
            type: Boolean
        }
    }
})
