import template from './template.html.twig'
import './style.scss'

const { Component, Data, Context } = Shopware 
const { Criteria } = Data 

export default Component.wrapComponentConfig({
    template,

    props: {
        slide: {
            type: Object,
            required: true,
        },

    },

    computed: {
        slideName () {
            return this.slide?.translated?.name ?? 'Loading...'
        },
        slideTitle () {
            return this.slide?.translated?.title ?? this.$tc('blurElysium.general.noHeadline')
        }
    },

    methods: {

        positionUp () {
            this.$emit('position-up', this.slide)
        },

        positionDown () {
            this.$emit('position-down', this.slide)
        },

        editSlide () {
            this.$emit('edit-slide', this.slide)
        },

        removeSlide () {
            this.$emit('remove-slide', this.slide)
        },

        startDrag (event) {
            this.$emit('start-drag', this.slide, event, this.$refs.selectItem)
        },

        enterDrag (event) {
            this.$emit('enter-drag', this.slide, event, this.$refs.selectItem)
        },

        endDrag (event) {
            this.$emit('end-drag', this.slide, event, this.$refs.selectItem)
        },

        leaveDrag (event) {
            this.$emit('leave-drag', this.slide, event, this.$refs.selectItem)
        }
    },
})