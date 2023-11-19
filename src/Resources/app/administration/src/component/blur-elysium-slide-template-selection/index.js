import template from './template.html.twig';

export default {
    template,

    props: {
        value: {
            type: String
        }
    },

    data() {
        return {
            defaultSlideTemplates: [
                {
                    label: this.$tc('blurElysiumSlides.slideSettings.slideTemplate.option.default'),
                    value: 'default'
                }
            ]
        };
    },

    watch: {
    },

    computed: {
    },

    created() {
    },

    mounted() {
    },

    methods: {
        onChange(value) {
            this.$emit('change', value)
        }
    }
};