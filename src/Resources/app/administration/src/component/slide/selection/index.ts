import template from './template.html.twig';
import './style.scss';

import { DragConfig, DragData, DropData } from '@elysium/types/dnd';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    props: {
        selectedSlides: {
            type: Array,
            required: true,
        },
    },

    data() {
        return {
            slide: null,
            draggedItemIndex: null,
        };
    },

    computed: {
        selectedSlidesIds() {
            return this.selectedSlides.map((slide) => slide.id);
        },
    },

    methods: {
        addSlide(slide: Entity<'blur_elysium_slides'>) {
            this.$emit('add-slide', slide);
        },

        removeSlide(slide: Entity<'blur_elysium_slides'>) {
            this.$emit('remove-slide', slide);
        },

        slidePositionUp(slide: Entity<'blur_elysium_slides'>) {
            this.$emit('position-up', slide);
        },

        slidePositionDown(slide: Entity<'blur_elysium_slides'>) {
            this.$emit('position-down', slide);
        },

        slideRemove(slide: Entity<'blur_elysium_slides'>) {
            this.$emit('remove-slide', slide);
        },

        slideEdit(slide: Entity<'blur_elysium_slides'>) {
            const route = this.$router.resolve({
                name: 'blur.elysium.slides.detail',
                params: { id: slide.id },
            });
            window.open(route.href, '_blank');
        },

        dragSlideStart(
            slide: Entity<'blur_elysium_slides'>,
            dragConfig: DragConfig,
            _draggedSlide: DragData
        ) {
            this.slide = slide;
            this.draggedItemIndex = dragConfig?.data?.draggedItemIndex;
            this.$emit(
                'drag-slide-start',
                slide,
                dragConfig?.data?.draggedItemIndex
            );
        },

        dragSlideDrop(dragData: DragData, dropData: DropData) {
            if (dropData === null) {
                return;
            }
            this.$emit(
                'drag-slide-drop',
                this.slide,
                this.draggedItemIndex,
                dropData.index
            );
        },
    },
});
