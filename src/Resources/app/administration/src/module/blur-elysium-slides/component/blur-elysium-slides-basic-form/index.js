import template from './blur-elysium-slides-basic-form.twig';

const { Criteria } = Shopware.Data;
const { Component, Context, Mixin } = Shopware;
const { mapPropertyErrors, mapState } = Shopware.Component.getComponentHelper();

const propErrors = [
    'name'
];

Component.register( 'blur-elysium-slides-basic-form', {
    template,

    props: {
        blurElysiumSlides: {
            type: Object,
            required: true,
        }
    },

    computed: {
    
        ...mapPropertyErrors( 'blurElysiumSlides' , propErrors)
    },

    created() {
    }
});