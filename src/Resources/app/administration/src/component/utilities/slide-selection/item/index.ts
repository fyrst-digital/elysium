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
        index: {
            type: Number,
            required: true,
        },
    },

    computed: {
        slideName () {
            return this.slide?.translated?.name ?? 'Loading...'
        },
        slideTitle () {
            return this.slide?.translated?.title ?? this.$tc('blurElysium.general.noHeadline')
        },
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

        dragStart (dragConfig: any) {
            this.$emit('drag-slide-start', this.slide, dragConfig)
        },

        dragEnter (dragData: any, dropData: any, validDrag: boolean) {
        },

        dragLeave (dragData: any, dropData: any, validDrag: boolean) {
        },

        validateDrop (dragData: any, dropData: any) {
            let isValid = false
            if(typeof dropData?.index === 'number' && dragData.draggedItemIndex !== dropData?.index) isValid = true
            return isValid
        },

        dropSlide (dragData: any, dropData: any) {
            this.$emit('drag-slide-drop', dragData, dropData)
        },
    },
})