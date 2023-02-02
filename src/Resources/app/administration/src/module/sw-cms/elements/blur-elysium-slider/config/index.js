import template from './blur-cms-el-config-elysium-slider.twig';
import './blur-cms-el-config-elysium-slider.scss';

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
        elysiumSlideCollection: {
            get() {
                return this.element.config.elysiumSlideCollection.value
            },

            set(newValue) {
                // Note: we are using destructuring assignment syntax here.
                this.element.config.elysiumSlideCollection.value = newValue
            }
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

                /**
                 * @todo clear slide id in collection if the elysium slide id doen't exist anymore
                 */
                    
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

        onCreateSlide() {
            let route = this.$router.resolve({name: 'blur.elysium.slides.create'})
            window.open(route.href, '_blank')
        },

        onSlideOverview() {
            let route = this.$router.resolve({name: 'blur.elysium.slides.index'})
            window.open(route.href, '_blank')
        }
    }
});