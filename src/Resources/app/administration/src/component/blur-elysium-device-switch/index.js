import template from './template.html.twig'

export default {
    template,

    props: {
        viewports: {
            type: Array,
            default () {
                return ['mobile', 'tablet', 'desktop']
            }
        },

        defaultViewport: {
            type: String,
            default: 'desktop'
        },

        layout: {
            type: String,
            /** @type 'default' | 'tab' */
            default: 'default'
        },

        justify: {
            type: String,
            /** @type 'start' | 'center' | 'end' */
            default: 'center'
        },

        gap: {
            type: Number,
            default: 30
        },

        showLabels: {
            type: Boolean,
            /** @type Boolean */
            default: true
        }
    },

    data () {
        return {
            activeViewport: this.defaultViewport
        }
    },

    computed: {
        containerStyle () {
            const styles = {
                display: 'flex',
                gap: `${this.gap}px`,
                justifyContent: this.justify,
                flexDirection: 'row'
            }

            if (this.layout === 'tab') {
                styles.padding = '15px 25px 0'
                styles.borderBottom = '1px solid #ddd'
                styles.marginBottom = '-1px'
            }

            return styles
        },

        itemStyle () {
            const styles = {
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                gap: '.5rem',
                cursor: 'pointer',
                userSelect: 'none'
            }

            if (this.layout === 'default') {
                styles.flexDirection = 'column'
            }

            if (this.layout === 'tab') {
                styles.borderBottom = '1px solid transparent'
                styles.paddingBottom = '15px'
                styles.flexDirection = 'row'
            }

            return styles
        }
    },

    methods: {
        conditionalItemStyle (viewport) {
            const styles = {
                color: viewport === this.activeViewport ? '#3498db' : '#758CA3'
            }

            if (this.layout === 'tab') {
                styles.borderBottomColor = viewport === this.activeViewport ? '#3498db' : 'transparent'
            }

            return styles
        },

        changeViewport (viewport) {
            this.activeViewport = viewport
            this.$emit('change-viewport', viewport)
        }
    }
}
