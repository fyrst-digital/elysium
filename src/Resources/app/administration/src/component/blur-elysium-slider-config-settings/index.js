// import './blur-elysium-slider-config-navigation.scss'
import template from './blur-elysium-slider-config-settings.twig';
import { containerBreakpoints } from "@elysiumSlider/utilities/layout";
import { config } from "@elysiumSlider/utilities/identifiers";

const { Criteria } = Shopware.Data;
const { Component, Context, Mixin } = Shopware;
const { mapPropertyErrors, mapState } = Shopware.Component.getComponentHelper();

export default {
    template,

    props: {
        config: {
            type: Object,
        }
    },

    data() {
        return {
            
        };
    },

    watch: {
    },

    computed: {
        positionIdentifiers() {
            return config
        },
        containerBreakpoints() {
            return containerBreakpoints;
        }

    },

    created() {
        console.log(this.config)
    }
};