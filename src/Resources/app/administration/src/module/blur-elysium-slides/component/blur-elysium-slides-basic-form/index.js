import template from './blur-elysium-slides-basic-form.twig';
import { slides } from "@elysiumSlider/utilities/identifiers";

const { Component, Mixin } = Shopware;
const { mapMutations, mapPropertyErrors, mapState } = Component.getComponentHelper();

const propErrors = [
    'name'
];

export default {
    template,

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('blur-editable')
    ],

    /**
     * @deprecated this props will be removed
     * allowEdit will be handled through the ACL vuex state
     */
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
            'slide',
            'loading',
            'acl'
        ]),
        
        ...mapPropertyErrors( 'blurElysiumSlides' , propErrors),

        positionIdentifiers() {
            return slides
        },

        urlOverlayActive() {
            return this.slide.slideSettings && this.slide.slideSettings.urlOverlay ? true : false
        }
    },

    created() {
        /**
         * @todo set editable props in template
         */
        console.log(this.editable, this.slide)
    },

    methods: {
        ...mapMutations('blurElysiumSlidesDetail', [
            'setSlideSetting',
        ]),
    }
}