import template from './template.html.twig'
import './style.scss';

const { Component, Mixin } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('blur-style-utilities'),
    ],

    props: {
        label: {
            type: String,
            required: false,
            default: 'Label',
        },
        placeholder: {
            type: String,
            required: false,
            default: 'Placeholder',
        },
        disabled: {
            type: Boolean,
            required: false,
            default: false,
        },
        required: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    data() {
        return {
            styles: {
                container: {
                    mobile: {
                        display: 'grid',
                        'grid-template-columns': 'auto minmax(0, 1fr)',
                        'gap': '16px',
                        alignItems: 'center',
                    }
                },
                label: {
                    mobile: {
                        fontSize: '14px',
                    }
                },
                input: {
                    mobile: {
                        fieldSizing: 'content',
                        maxWidth: '100%',
                        minWidth: '120px',
                        marginLeft: 'auto',
                        padding: '6px 12px',
                        borderRadius: '5px',
                        border: '1px solid #dddddd',
                    }
                }
            }
        };
    },

    computed: {
    }
});
