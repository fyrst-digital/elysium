import template from './template.html.twig'

export default {
    template,

    props: {
        title: {
            type: String,
            default: 'Title'
        },

        content: {
            type: String | Boolean,
            default: false
        }
    }
}
