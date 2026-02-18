import template from './template.html.twig';
import './style.scss';

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
            dragConfig: unknown,
            _draggedSlide: unknown
        ) {
            this.slide = slide;
            this.draggedItemIndex = (dragConfig as { data?: { draggedItemIndex?: number } })?.data?.draggedItemIndex;
            this.$emit(
                'drag-slide-start',
                slide,
                (dragConfig as { data?: { draggedItemIndex?: number } })?.data?.draggedItemIndex
            );
        },

        dragSlideDrop(dragData: unknown, dropData: unknown) {
            if (dropData === null) {
                return;
            }
            this.$emit(
                'drag-slide-drop',
                this.slide,
                this.draggedItemIndex,
                (dropData as { index?: number })?.index
            );
        },
    },
});
