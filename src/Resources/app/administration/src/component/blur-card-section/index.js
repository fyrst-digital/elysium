import template from './template.html.twig'

export default {
    template,

    props: {
        last: {
            type: Boolean,
            default: false
        }
    },

    computed: {
        styles () {
            const styles = {}

            if (this.last === false) {
                styles.borderBottom = '1px solid var(--color-gray-200)'
                styles.marginBottom = '30px'
            }

            return styles
        }
    }
}
