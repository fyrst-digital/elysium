import template from './template.html.twig';

export default {
    template,

    props: {
        value: {
            type: String
        },
        disabled: {
            type: Boolean
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

    methods: {
        onChange(value) {
            this.$emit('change', value)
        }
    }
};