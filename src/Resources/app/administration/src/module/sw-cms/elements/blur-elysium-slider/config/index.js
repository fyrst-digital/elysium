import template from './blur-cms-el-config-elysium-slider.twig';

const { Component, Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

Shopware.Component.register( 'blur-cms-el-config-elysium-slider', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        'cms-element'
    ],

    data() {
        return {
            blurElysiumSlides: null,
            labelProp: "label",
            selectModel: []
        };
    },

    computed: {
        elysiumSlideCollection() {
            return this.element.config.elysiumSlideCollection.value;
        },

        elysiumSlidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        },

        defaultCriteria() {
            const defaultCriteria = new Criteria();

            defaultCriteria.addSorting( Criteria.sort(
                'name', 
                'ASC', 
                true
            ));


            return defaultCriteria;
        },

        context() {
            return { ...Shopware.Context.api };
        },
    },

    created() {
        this.getSlides();
        this.createdComponent();
    },

    methods: {

        createdComponent() {
            this.initElementConfig( 'blur-elysium-slider' );
        },

        getSlides() {
            this.elysiumSlidesRepository.search( this.defaultCriteria, Shopware.Context.api ).then(( res ) => {
                this.blurElysiumSlides = res;
            }).catch( ( e ) => {
                console.warn( e );
            });
        },

        onChange( content ) {
            this.emitChanges(content);
        },


        emitChanges( content ) {
            if (content !== this.element.config.elysiumSlideCollection.value) {
                this.element.config.elysiumSlideCollection.value = content;
                this.$emit('element-update', this.element);
            }
        },


        onElementUpdate(element) {
            // this.element.config.elysiumSlideCollection = element;
        }

    }
});