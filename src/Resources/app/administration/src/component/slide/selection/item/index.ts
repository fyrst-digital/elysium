import template from './template.html.twig';
import './style.scss';

const { Component } = Shopware;

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
        slideName() {
            return this.slide?.translated?.name ?? 'Loading...';
        },
        slideTitle() {
            return (
                this.slide?.translated?.title ??
                this.$tc('blurElysium.general.noHeadline')
            );
        },
    },

    methods: {
        positionUp() {
            this.$emit('position-up', this.slide);
        },

        positionDown() {
            this.$emit('position-down', this.slide);
        },

        editSlide() {
            this.$emit('edit-slide', this.slide);
        },

        removeSlide() {
            this.$emit('remove-slide', this.slide);
        },

        dragStart(dragConfig: unknown) {
            this.$emit('drag-slide-start', this.slide, dragConfig);
        },

        dragEnter(_dragData: unknown, _dropData: unknown, _validDrag: boolean) {},

        dragLeave(_dragData: unknown, _dropData: unknown, _validDrag: boolean) {},

        validateDrop(dragData: unknown, dropData: unknown) {
            let isValid = false;
            if (
                typeof (dropData as { index?: number })?.index === 'number' &&
                (dragData as { draggedItemIndex?: number })?.draggedItemIndex !== (dropData as { index?: number })?.index
            )
                isValid = true;
            return isValid;
        },

        dropSlide(dragData: unknown, dropData: unknown) {
            this.$emit('drag-slide-drop', dragData, dropData);
        },
    },
});
