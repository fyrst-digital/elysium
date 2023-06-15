import template from './blur-elysium-slides-basic-form.twig';
import { slides } from "@elysiumSlider/utilities/identifiers";

const { Criteria } = Shopware.Data;
const { Component, Context, Mixin } = Shopware;
const { mapPropertyErrors, mapState } = Shopware.Component.getComponentHelper();

const propErrors = [
    'name'
];

export default {
    template,

    mixins: [
        Mixin.getByName('placeholder'),
    ],

    props: {
        isLoading: {
            type: Boolean
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
        
        ...mapPropertyErrors( 'blurElysiumSlides' , propErrors),

        positionIdentifiers() {
            return slides
        },

        urlOverlayActive() {
            return this.slide.slideSettings && this.slide.slideSettings.urlOverlay ? true : false
        }
    }
}