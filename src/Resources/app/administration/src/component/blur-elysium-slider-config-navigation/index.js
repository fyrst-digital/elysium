// import './blur-elysium-slider-config-navigation.scss'
import template from './blur-elysium-slider-config-navigation.twig';
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
            positions: [
                { 
                    value: "below_slider", 
                    label: this.$tc('blurElysiumSlider.config.navigation.position.belowSlider') 
                },
                { 
                    value: "in_slider", 
                    label: this.$tc('blurElysiumSlider.config.navigation.position.inSlider') 
                }
            ],
            aligns: [
                { 
                    value: "start", 
                    label: this.$tc('blurElysiumSlider.config.navigation.align.left') 
                },
                { 
                    value: "center", 
                    label: this.$tc('blurElysiumSlider.config.navigation.align.center') 
                },
                { 
                    value: "end", 
                    label: this.$tc('blurElysiumSlider.config.navigation.align.right') 
                }
            ],
            shapes: [
                { 
                    value: "circle", 
                    label: this.$tc('blurElysiumSlider.config.navigation.shape.circle') 
                },
                { 
                    value: "bar", 
                    label: this.$tc('blurElysiumSlider.config.navigation.shape.bar') 
                }
            ],
            navPaddings: [
                { 
                    value: "sm", 
                    label: this.$tc('blurElysiumSlider.general.small') 
                },
                { 
                    value: "md", 
                    label: this.$tc('blurElysiumSlider.general.medium') 
                },
                { 
                    value: "lg", 
                    label: this.$tc('blurElysiumSlider.general.large') 
                }
            ],
            sizes: [
                { 
                    value: "sm", 
                    label: this.$tc('blurElysiumSlider.general.small') 
                },
                { 
                    value: "md", 
                    label: this.$tc('blurElysiumSlider.general.medium') 
                },
                { 
                    value: "lg", 
                    label: this.$tc('blurElysiumSlider.general.large') 
                }
            ]
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

    }
};