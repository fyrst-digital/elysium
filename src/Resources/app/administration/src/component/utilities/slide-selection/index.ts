import template from './template.html.twig'
import './style.scss'
import { watch } from 'vue'

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
            selectedSlides: {},
            currentDragIndex: 0,
            draggedSlideId: null,
            draggedSlideElement: null,
            placeholderElement: document.createElement('div')
        }
    },

    watch: {
        selectedSlides: {
            handler () {
                /**
                 * because this.selectedSlidesIds is an immutable prop we have to clear the array first
                 * then we can add the ids from selectedSlides collection via push to the selected slides element config
                 * @todo it's a little bit clunky but it works for now. maybe find a better solution in the future
                */
                this.selectedSlidesIds.length = 0
                this.selectedSlidesIds.push(...this.selectedSlides.map((slide) => slide.id))
            },
            deep: false
        }
    },

    computed: {
        slidesRepository () {
            return this.repositoryFactory.create('blur_elysium_slides')
        },
    },

    mounted () {
        this.placeholderElement.style.borderRadius = '12px'
        this.placeholderElement.style.backgroundColor = '#eeeeee'
    },

    methods: {

        initSlides () {
            const criteria = new Criteria()
            criteria.setIds(this.selectedSlidesIds)

            this.slidesRepository.search(criteria, Context.api).then((result) => {

                /**
                 * filter the selected slides to remove orphaned selections
                 * @todo check if the filter funtion is really needed. Because the selectedSlidesIds are already set in the criteria `criteria.setIds(this.selectedSlidesIds)`
                 */
                const filteredSlides = result.filter((slide) => this.selectedSlidesIds.includes(slide.id))
                this.selectedSlides = filteredSlides

                /**
                 * if the filtered slides are empty, then clear all selected slides Ids
                 * @todo if the filtered slides not empty, check them against the selectedSlidesIds
                 */
                if (filteredSlides.length <= 0) {
                    this.selectedSlidesIds.length = 0
                }

                this.isLoading = false
            }).catch((error) => {
                console.error('Error loading slides', error)
            })
        },

        selectSlide (slide) {
            // call if slide is selected
            // add slide to this.selectedSlidesIds if is not in collection
            // remove slide from this.selectedSlidesIds if is in collection
            const index = this.selectedSlidesIds.indexOf(slide.id)

            switch (this.selectedSlidesIds.includes(slide.id)) {
                case true:
                    /** @action remove slide **/
                    this.selectedSlides.remove(slide.id)
                    break
                default:
                    /** @action add slide **/
                    this.selectedSlides.add(slide)
                    break
            }

            console.log('selectSlideNew', slide, this.selectedSlides)
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
            const currentIndex = this.selectedSlides.indexOf(slide)
            if (currentIndex > 0) this.selectedSlides.moveItem(currentIndex, currentIndex - 1)
        },

        slidePositionDown (slide) {
            const currentIndex = this.selectedSlides.indexOf(slide)
            if (currentIndex < this.selectedSlides.length - 1) this.selectedSlides.moveItem(currentIndex, currentIndex + 1)
        },

        slideRemove (slide) {
            this.selectedSlides.remove(slide.id)
        },

        slideEdit (slide) {
            const route = this.$router.resolve({ name: 'blur.elysium.slides.detail', params: { id: slide.id } })
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
        console.log('selectedSlidesIds', this.selectedSlidesIds)
    },
})
