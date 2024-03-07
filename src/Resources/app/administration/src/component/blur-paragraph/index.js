import template from './template.html.twig'

export default {
    template,

    props: {
        title: {
            type: String | Boolean,
            default: false
        },

        content: {
            type: String | Boolean,
            default: false
        }
    }
}
