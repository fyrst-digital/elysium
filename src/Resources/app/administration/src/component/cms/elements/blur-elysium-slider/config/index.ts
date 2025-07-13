import template from './template.html.twig';

const { Component, Mixin, Store, Data, Context } = Shopware;
const { Criteria } = Data;

export default Component.wrapComponentConfig({
    template,

    mixins: [Mixin.getByName('cms-state'), Mixin.getByName('cms-element')],

    inject: ['repositoryFactory'],

    data() {
        return {
            activeTab: 'content',
            selectedSlides: []
        };
    },

    computed: {
        cmsPage() {
            return Store.get('cmsPage');
        },

        elysiumCms() {
            return Store.get('elysiumCMS');
        },

        selectedSlidesIds() {
            return this.selectedSlides.map((slide: Entity<'blur_elysium_slides'>) => slide.id);
        },

        device() {
            if (this.cmsPage.currentCmsDeviceView === 'tablet-landscape') {
                return 'tablet';
            }

            return this.cmsPage.currentCmsDeviceView;
        },

        slidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        },

        tabs() {
            return [
                {
                    label: this.$tc('blurElysiumSlider.config.contentLabel'),
                    name: 'content',
                },
                {
                    label: this.$tc('blurElysiumSlider.config.settingsLabel'),
                    name: 'settings',
                },
                {
                    label: this.$tc('blurElysiumSlider.config.sizingLabel'),
                    name: 'sizing',
                },
                {
                    label: this.$tc('blurElysiumSlider.config.navigationLabel'),
                    name: 'navigation',
                },
                {
                    label: this.$tc('blurElysiumSlider.config.arrowsLabel'),
                    name: 'arrows',
                },
            ];
        },
    },

    methods: {
        changeDevice(device: string) {
            this.cmsPage.setCurrentCmsDeviceView(
                device === 'tablet' ? 'tablet-landscape' : device
            );
        },

        loadSelectedSlides(selectedSlidesIds: string[]) {
            const criteria = new Criteria();
            criteria.setIds(selectedSlidesIds);

            this.slidesRepository
                .search(criteria, Context.api)
                .then((result: EntityCollection<'blur_elysium_slides'>) => {
                    const filteredSlides = result.filter((slide) =>
                        selectedSlidesIds.includes(slide.id)
                    );
                    this.selectedSlides = filteredSlides;
                })
                .catch((error) => {
                    console.error('Error loading slides', error);
                });
        },

        addSlide(slide: Entity<'blur_elysium_slides'>) {
            this.selectedSlides.push(slide);
        },

        removeSlide(slide: Entity<'blur_elysium_slides'>) {
            const index = this.selectedSlides.findIndex(
                (selectedSlide: Entity<'blur_elysium_slides'>) => selectedSlide.id === slide.id
            );
            if (index !== -1) this.selectedSlides.splice(index, 1);
        },

        moveUpSlide(slide: Entity<'blur_elysium_slides'>) {
            const currentIndex = this.selectedSlides.indexOf(slide);
            const toIndex = currentIndex - 1;

            if (currentIndex > 0)
                this.selectedSlides.splice(currentIndex, 1);
                this.selectedSlides.splice(toIndex, 0, slide);
        },

        moveDownSlide(slide: Entity<'blur_elysium_slides'>) {
            const currentIndex = this.selectedSlides.indexOf(slide);
            const toIndex = currentIndex + 1;

            if (currentIndex < this.selectedSlides.length - 1) {
                this.selectedSlides.splice(currentIndex, 1);
                this.selectedSlides.splice(toIndex, 0, slide);
            }
        },

        dragSlideDrop(
            slide: Entity<'blur_elysium_slides'>,
            fromIndex: number,
            toIndex: number
        ) {
            this.selectedSlides.splice(fromIndex, 1);
            this.selectedSlides.splice(toIndex, 0, slide);
        },
    },

    watch: {
        selectedSlidesIds: {
            handler(slidesIds: string[]) {
                this.element.config.elysiumSlideCollection.value = slidesIds;
            },
        },
    },

    created() {
        this.initElementConfig('blur-elysium-slider');
        if (this.element.config.elysiumSlideCollection.value.length > 0) {
            this.loadSelectedSlides(
                this.element.config.elysiumSlideCollection.value
            );
        }
    },
});
