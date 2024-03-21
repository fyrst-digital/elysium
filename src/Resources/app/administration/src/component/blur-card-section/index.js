import template from './template.html.twig'
import './style.scss'

export default {
    template,

    props: {
        positionIdentifier: {
            type: String,
            required: false,
            default: null,
        },

        last: {
            type: Boolean,
            default: false
        }
    },

    computed: {
        classes () {
            const classes = ['blur-card-section']

            if (this.last === true) {
                classes.push('last')
            }

            return classes
        }
    }
}
