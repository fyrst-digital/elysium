import template from './blur-elysium-slides-content-container-settings.twig';
import { slides } from "@elysiumSlider/utilities/identifiers";

const { Criteria } = Shopware.Data;
const { Component, Context, Mixin } = Shopware;
const { mapPropertyErrors, mapState } = Shopware.Component.getComponentHelper();


/**
 * @deprecated this whole component will be removed
 */

export default {
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
        
        positionIdentifiers() {
            return slides
        },
        
        ...mapState('blurElysiumSlidesDetail', [
            'slide'
        ]),
    }
}