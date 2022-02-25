import template from './blur-elysium-slides-settings-form.twig';

const { Criteria } = Shopware.Data;
const { Component, Context, Mixin } = Shopware;
const { mapPropertyErrors, mapState } = Shopware.Component.getComponentHelper();

Component.register( 'blur-elysium-slides-settings-form', {
    template,

    mixins: [
        Mixin.getByName('placeholder'),
    ],

    props: {
        isLoading: {
            type: Boolean,
            required: false,
            default: true,
        },
        allowEdit: {
            type: Boolean,
            required: false,
            default: true,
        }
    },

    computed: {
        
        ...mapState('blurElysiumSlidesDetail', [
            'slide'
        ]),
    },

    created() {
        console.dir( this );
    }
});