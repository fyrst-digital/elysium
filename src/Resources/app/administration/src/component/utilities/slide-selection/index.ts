import template from './template.html.twig'
import './style.scss'

const { Component, Data, Context } = Shopware 
const { Criteria } = Data 

export default Component.wrapComponentConfig({
    template,

    inject: [
        'repositoryFactory'
    ],

    props: {
        selectedSlides: {
            type: Array,
            required: true,
        }
    },

    data () {
        return {
            searchTerm: '',
            searchFocus: false,
            slidesCollection: {},
            currentDragIndex: 0,
            draggedSlideId: null,
            draggedSlideElement: null,
            placeholderElement: document.createElement('div')
        }
    },

    watch: {
        searchFocus(value: boolean) {

            if (value === true) {
                this.loadSlides()
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

    mounted () {
        this.placeholderElement.style.borderRadius = '12px'
        this.placeholderElement.style.backgroundColor = '#eeeeee'
    },

    methods: {
        inputSearch () {
            this.loadSlides()
        },

        focusSearch () {
            this.searchFocus = true
        },

        blurSearch (event) {
            if (event.relatedTarget !== null) {
                this.searchFocus = false
            }
        },

        loadSlides () {
            this.slidesRepository.search(this.slidesCriteria, Context.api).then((result) => {
                this.slidesCollection = result
            })
        },

        selectSlide (value) {
            // call if slide is selected
            // add slide to this.selectedSlides if is not in collection
            // remove slide from this.selectedSlides if is in collection
            const index = this.selectedSlides.indexOf(value.id)

            switch (this.selectedSlides.includes(value.id)) {
                case false:
                    this.selectedSlides.push(value.id)
                    break
                case true:
                    this.selectedSlides.splice(index, 1)
                    break
                default:
                    this.selectedSlides.push(value.id)
                    break
            }

            // loose search focus
            this.searchFocus = false
        },

        slideIsSelected (slide) {
            if (this.selectedSlides.includes(slide.id)) {
                return true
            }
            return false
        },

        slideLoaded (slide, slideId: string) {
            if (slide === null) {
                this.onRemoveSlide(slideId)
            }
        },

        onDrop () {
            this.selectedSlides.splice(this.selectedSlides.indexOf(this.draggedSlideId), 1)
            this.draggedSlideElement.classList.remove('is-dragged')
            this.selectedSlides.splice(this.currentDragIndex, 0, this.draggedSlideId)
            this.placeholderElement.remove()
        },

        dragEnd (event) {
            event.target.classList.remove('is-dragged')
            this.placeholderElement.remove()
        },

        startDrag (slideId, event, element) {
            this.draggedSlideId = slideId
            this.draggedSlideElement = element
            this.placeholderElement.style.height = `${element.offsetHeight}px`
        },

        onDrag (slideId, event, element) {
            if (this.draggedSlideId === slideId) {
                this.draggedSlideElement.classList.add('is-dragged')
            }

            if (this.currentDragIndex !== this.selectedSlides.indexOf(slideId)) {
                element.before(this.placeholderElement)
            } else {
                element.after(this.placeholderElement)
            }

            this.currentDragIndex = this.selectedSlides.indexOf(slideId)
        },

        slidePositionUp (slide) {
            const currentIndexPos = this.selectedSlides.indexOf(slide)
            const upIndexPos = currentIndexPos - 1

            if (upIndexPos >= 0) {
                // remove slide from current position
                this.selectedSlides.splice(currentIndexPos, 1)
                // add slide on up position
                this.selectedSlides.splice(upIndexPos, 0, slide)
            }
        },

        slidePositionDown (slide) {
            const currentIndexPos = this.selectedSlides.indexOf(slide)
            const downIndexPos = currentIndexPos + 1
            const maxIndex = this.selectedSlides.length - 1

            if (currentIndexPos < maxIndex) {
                // remove slide from current position
                this.selectedSlides.splice(currentIndexPos, 1)
                // add slide on down position
                this.selectedSlides.splice(downIndexPos, 0, slide)
            }
        },

        onRemoveSlide (slide) {
            this.selectedSlides.splice(this.selectedSlides.indexOf(slide), 1)
        },

        onEditSlide (slide) {
            const route = this.$router.resolve({ name: 'blur.elysium.slides.detail', params: { id: slide } })
            window.open(route.href, '_blank')
        },

        onCreateSlide () {
            const route = this.$router.resolve({ name: 'blur.elysium.slides.create' })
            window.open(route.href, '_blank')
        },

        onSlideOverview () {
            const route = this.$router.resolve({ name: 'blur.elysium.slides.overview' })
            window.open(route.href, '_blank')
        },
    },

    created() {
        this.loadSlides()
    },
})
