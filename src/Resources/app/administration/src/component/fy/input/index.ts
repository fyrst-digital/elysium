import template from './template.html.twig'
import './style.scss';

const { Component, Mixin } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('blur-style-utilities'),
    ],

    props: {
        modelValue: {
            type: String,
            required: false,
            default: '',
        },
        id: {
            type: String,
            required: true,
        },
        layout: {
            type: String,
            required: false,
            default: 'row',
            validator(value: string) {
                return ['row', 'column'].includes(value);
            },
        },
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
        tooltip: {
            type: [String, undefined],
            required: false,
            default: undefined,
        },
        error: {
            type: [Object, Boolean],
            required: false,
            default: false,
        }
    },

    emits: ['update:modelValue'],
});