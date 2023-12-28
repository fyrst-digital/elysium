import template from './template.twig';
import { config } from "@elysiumSlider/utilities/identifiers";

const { Component, Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

Shopware.Component.register( 'blur-cms-el-config-elysium-banner', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        'cms-element'
    ],

    data() {
        return {
            blurElysiumSlide: null,
            labelProp: "label",
            selectModel: [],
            activeViewport: 'desktop'
        };
    },

    computed: {
        positionIdentifiers() {
            return config
        },

        selectedSlide: {
            get() {
                return this.element.config.elysiumSlide.value
            },

            set(newValue) {
                this.element.config.elysiumSlide.value = newValue
            }
        },

        elysiumSlidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        },

        defaultCriteria() {
            const defaultCriteria = new Criteria();

            defaultCriteria.addSorting(Criteria.sort(
                'name', 
                'ASC', 
                true
            ));


            return defaultCriteria;
        },

        context() {
            return { ...Shopware.Context.api };
        }
    },

    created() {
        this.initElementConfig('blur-elysium-banner')
        console.log(this.viewports, this.element.config.viewports.value[this.activeViewport])
    },

    watch: {
    },

    methods: {
        onChangeViewport(viewport) {
            this.activeViewport = viewport
        }
    }
});