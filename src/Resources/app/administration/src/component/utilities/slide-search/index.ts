import template from './template.html.twig'

const { Component, Data, Context } = Shopware 
const { Criteria } = Data 

export default Component.wrapComponentConfig({
    template,

    inject: [
        'repositoryFactory',
    ],

    props: {
        selectedSlidesIds: {
            type: Array,
            required: true,
        }
    },

    data () {
        return {
            searchTerm: '',
            searchFocus: false,
            slidesCollection: {},
            slidesLoading: true,
        }
    },

    watch: {

        searchFocus(value: boolean) {
            if (value === true) {
                this.loadSlides()
            } else {
                this.slidesCollection = {}
            }
        },

        searchTerm () {
            this.loadSlides()
        }
    },

    computed: {
        slidesRepository () {
            return this.repositoryFactory.create('blur_elysium_slides')
        },

        slidesCriteria () {
            const criteria = new Criteria()

            criteria.setTerm(this.searchTerm)
            criteria.setLimit(20)

            return criteria
        },
    },

    methods: {
        focusSearch () {
            this.slidesLoading = true
            this.searchFocus = true
        },

        blurSearch () {
            this.searchFocus = false
        },

        loadSlides () {
            this.slidesRepository.search(this.slidesCriteria, Context.api).then((result) => {
                this.slidesCollection = result
                this.slidesLoading = false
            }).catch((error) => {
                console.error('Error loading slides', error)
            })
        },

        selectSlide (slide) {
            this.searchFocus = false
            this.$emit('slide-selected', slide)
        },

        slideIsSelected (slide) {
            if (this.selectedSlidesIds.includes(slide.id)) {
                return true
            }
            return false
        },
    }
})
