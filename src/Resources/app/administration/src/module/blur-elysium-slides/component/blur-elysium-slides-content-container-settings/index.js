import template from './blur-elysium-slides-content-container-settings.twig';

const { Criteria } = Shopware.Data;
const { Component, Context, Mixin } = Shopware;
const { mapPropertyErrors, mapState } = Shopware.Component.getComponentHelper();

Component.register( 'blur-elysium-slides-content-container-settings', {
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
    }
});