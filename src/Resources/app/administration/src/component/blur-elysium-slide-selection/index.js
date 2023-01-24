import template from './template.twig';

const { Criteria } = Shopware.Data;
const { Component, Context, Mixin } = Shopware;
const { mapPropertyErrors, mapState } = Shopware.Component.getComponentHelper();

const propErrors = [
    'name'
];

Component.register( 'blur-elysium-slide-selection', {
    template,

    inject: [
        'repositoryFactory'
    ],

    props: {
        selectedSlides: {
            type: Array
        }
    },

    data() {
        return {
            searchTerm: '',
            searchFocus: false,
            elysiumSlides: []
        };
    },

    watch: {
        selectedSlides( slides, oldSlides ) {
            //this.$emit('updateSelectedSlides', this.slides)
        },
        searchTerm( currentTerm, originTerm ) {
            this.inputSearch()
        }
    },

    computed: {
        elysiumSlidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        },

        elysiumSlidesCriteria() {
            const criteria = new Criteria()

            criteria.setTerm(this.searchTerm)

            return criteria
        }
    },

    created() {
        this.getElysiumSlides()
    },

    mounted() {
    },

    methods: {
        inputSearch() {
            // execute if user types a search term
            this.getElysiumSlides()
        },

        focusSearch() {
            // call if search is focused
            this.searchFocus = true
        },

        blurSearch( event ) {
            // call if search is focused
            if ( event.relatedTarget !== null ) {
                this.searchFocus = false
            }
        },

        getElysiumSlides() {
            this.elysiumSlidesRepository.search( this.elysiumSlidesCriteria, Shopware.Context.api ).then((result) => {
                this.elysiumSlides = result
            })
        },

        selectSlide( value ) {
            // call if slide is selected
            // add slide to this.selectedSlides if is not in collection
            // remove slide from this.selectedSlides if is in collection
            let index = this.selectedSlides.indexOf(value.id)
            
            switch (this.selectedSlides.includes(value.id)) {
                case false:
                    this.selectedSlides.push( value.id )
                    break;
                case true:
                    this.selectedSlides.splice( index, 1 )
                    break;
                default:
                    this.selectedSlides.push( value.id )
                    break;
            }

            // loose search focus
            this.searchFocus = false
        },

        slideIsSelected( slide ) {
            if (this.selectedSlides.includes(slide.id)) {
                return true
            }
            return false
        }
    }
});