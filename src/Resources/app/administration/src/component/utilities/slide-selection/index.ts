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
        selectedSlidesIds: {
            type: Array,
            required: true,
        }
    },

    data () {
        return {
            isLoading: true,
            searchTerm: '',
            searchFocus: false,
            selectedSlidesCollection: {},
            slidesCollection: {},
            currentDragIndex: 0,
            draggedSlideId: null,
            draggedSlideElement: null,
            placeholderElement: document.createElement('div')
        }
    },

    watch: {
        searchFocus(value: boolean) {

            console.log('searchFocus', value)

            if (value === true) {
                this.loadSlides()
            }
        },

        searchTerm () {
            console.log('searchTerm', this.searchTerm)
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

        initSlides () {
            console.log('initSlides', this.selectedSlidesIds)
            const criteria = new Criteria()
            criteria.setIds(this.selectedSlidesIds)

            this.slidesRepository.search(criteria, Context.api).then((result) => {
                console.log('initSlides', result.filter((slide) => this.selectedSlidesIds.includes(slide.id)))
                this.selectedSlidesCollection = result.filter((slide) => this.selectedSlidesIds.includes(slide.id))
                this.isLoading = false
            })
            // this.loadSlides()
        },

        loadSlides () {
            this.slidesRepository.search(this.slidesCriteria, Context.api).then((result) => {
                this.slidesCollection = result
                this.isLoading = false
            })
        },

        selectSlide (value) {
            // call if slide is selected
            // add slide to this.selectedSlidesIds if is not in collection
            // remove slide from this.selectedSlidesIds if is in collection
            const index = this.selectedSlidesIds.indexOf(value.id)

            switch (this.selectedSlidesIds.includes(value.id)) {
                case false:
                    this.selectedSlidesIds.push(value.id)
                    break
                case true:
                    this.selectedSlidesIds.splice(index, 1)
                    break
                default:
                    this.selectedSlidesIds.push(value.id)
                    break
            }

            // loose search focus
            this.searchFocus = false
        },

        slideIsSelected (slide) {
            if (this.selectedSlidesIds.includes(slide.id)) {
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
            this.selectedSlidesIds.splice(this.selectedSlidesIds.indexOf(this.draggedSlideId), 1)
            this.draggedSlideElement.classList.remove('is-dragged')
            this.selectedSlidesIds.splice(this.currentDragIndex, 0, this.draggedSlideId)
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

            if (this.currentDragIndex !== this.selectedSlidesIds.indexOf(slideId)) {
                element.before(this.placeholderElement)
            } else {
                element.after(this.placeholderElement)
            }

            this.currentDragIndex = this.selectedSlidesIds.indexOf(slideId)
        },

        slidePositionUp (slide) {
            const currentIndexPos = this.selectedSlidesIds.indexOf(slide)
            const upIndexPos = currentIndexPos - 1

            if (upIndexPos >= 0) {
                // remove slide from current position
                this.selectedSlidesIds.splice(currentIndexPos, 1)
                // add slide on up position
                this.selectedSlidesIds.splice(upIndexPos, 0, slide)
            }
        },

        slidePositionDown (slide) {
            const currentIndexPos = this.selectedSlidesIds.indexOf(slide)
            const downIndexPos = currentIndexPos + 1
            const maxIndex = this.selectedSlidesIds.length - 1

            if (currentIndexPos < maxIndex) {
                // remove slide from current position
                this.selectedSlidesIds.splice(currentIndexPos, 1)
                // add slide on down position
                this.selectedSlidesIds.splice(downIndexPos, 0, slide)
            }
        },

        onRemoveSlide (slide) {
            this.selectedSlidesIds.splice(this.selectedSlidesIds.indexOf(slide), 1)
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
        this.initSlides()
    },
})
