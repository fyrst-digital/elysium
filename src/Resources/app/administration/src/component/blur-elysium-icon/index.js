import template from './template.html.twig'

export default {
    template,

    props: {
        color: {
            type: String,
            default: 'currentColor'
        },
        size: {
            type: Number,
            default: 24
        }
    }
}