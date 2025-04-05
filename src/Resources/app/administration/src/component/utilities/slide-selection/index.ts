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
        },
    },

    data () {
        return {
            isLoading: true,
            currentDragIndex: 0,
            draggedSlide: null,
            draggedSlideElement: null,
            placeholderElement: document.createElement('div')
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

        addSlide (slide) {
            this.$emit('add-slide', slide)
        },

        removeSlide (slide) {
            this.$emit('remove-slide', slide)
        },

        onDrop () {
            this.selectedSlides.moveItem(this.selectedSlides.indexOf(this.draggedSlide), this.currentDragIndex)
            this.draggedSlideElement.classList.remove('is-dragged')
            this.placeholderElement.remove()
        },

        dragEnd (event) {
            event.target.classList.remove('is-dragged')
            this.placeholderElement.remove()
        },

        startDrag (slide, event, element) {
            console.log('start drag', slide, event, element)
            this.draggedSlide = slide
            this.draggedSlideElement = element
            this.placeholderElement.style.height = `${element.offsetHeight}px`
        },

        onDrag (slide, event, element) {
            if (this.draggedSlide === slide) {
                this.draggedSlideElement.classList.add('is-dragged')
            }

            if (this.currentDragIndex !== this.selectedSlides.indexOf(slide)) {
                element.before(this.placeholderElement)
            } else {
                element.after(this.placeholderElement)
            }
            
            this.currentDragIndex = this.selectedSlides.indexOf(slide)
        },

        slidePositionUp (slide: Entity<'blur_elysium_slides'>) {
            this.$emit('position-up', slide)
        },

        slidePositionDown (slide: Entity<'blur_elysium_slides'>) {
            this.$emit('position-down', slide)
        },

        slideRemove (slide: Entity<'blur_elysium_slides'>) {
            this.$emit('remove-slide', slide)
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
})
