import template from './template.html.twig';
import './style.scss';

import { DragConfig, DragData, DropData } from '@elysium/types/dnd';

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

        dragStart(dragConfig: DragConfig) {
            this.$emit('drag-slide-start', this.slide, dragConfig);
        },

        dragEnter(_dragData: DragData, _dropData: DropData, _validDrag: boolean) {},

        dragLeave(_dragData: DragData, _dropData: DropData, _validDrag: boolean) {},

        validateDrop(dragData: DragData, dropData: DropData) {
            let isValid = false;
            if (
                typeof dropData?.index === 'number' &&
                dragData?.draggedItemIndex !== dropData?.index
            )
                isValid = true;
            return isValid;
        },

        dropSlide(dragData: DragData, dropData: DropData) {
            this.$emit('drag-slide-drop', dragData, dropData);
        },
    },
});
