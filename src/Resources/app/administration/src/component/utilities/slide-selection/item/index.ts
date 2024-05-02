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
        selectedSlide: {
            type: String,
            required: true,
        },

    },

    data() {
        return {
            slideItem: {},
            isLoading: true
        }
    },

    computed: {
        slidesRepository () {
            return this.repositoryFactory.create('blur_elysium_slides')
        },
        slideCriteria () {
            const criteria = new Criteria()

            return criteria
        },
        slideName () {
            return this.slideItem?.translated?.name ?? 'Loading...'
        },
        slideTitle () {
            return this.slideItem?.translated?.title ?? this.$tc('blurElysium.general.noHeadline')
        }
    },

    methods: {
        loadSlide () {
            this.slidesRepository.get(this.selectedSlide, Context.api, this.slideCriteria).then((result) => {
                this.$emit('slide-loaded', result, this.selectedSlide)
                this.slideItem = result
                this.isLoading = false
            })
        },

        positionUp () {
            this.$emit('position-up', this.selectedSlide)
        },

        positionDown () {
            this.$emit('position-down', this.selectedSlide)
        },

        editSlide () {
            this.$emit('edit-slide', this.selectedSlide)
        },

        removeSlide () {
            this.$emit('remove-slide', this.selectedSlide)
        },

        startDrag (event: any) {
            this.$emit('start-drag', this.selectedSlide, event, this.$refs.selectItem)
        },

        enterDrag (event: any) {
            this.$emit('enter-drag', this.selectedSlide, event, this.$refs.selectItem)
        },

        endDrag (event: any) {
            this.$emit('end-drag', this.selectedSlide, event, this.$refs.selectItem)
        },

        leaveDrag (event: any) {
            this.$emit('leave-drag', this.selectedSlide, event, this.$refs.selectItem)
        }
    },

    created() {
        this.loadSlide()
    },
})