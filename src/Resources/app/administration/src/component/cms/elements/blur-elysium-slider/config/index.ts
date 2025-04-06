import template from './template.html.twig'

const { Component, Mixin, Store, Data, Context } = Shopware
const { Criteria } = Data 

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('cms-state'),
        Mixin.getByName('cms-element'),
    ],

    inject: [
        'repositoryFactory'
    ],

    // provide() {
    //     return {
    //         selectedSlidesIds: this.element.config.elysiumSlideCollection.value
    //     }
    // },
    
    data() {
        return {
            activeTab: 'content'
        }
    },

    computed: {
        cmsPage () {
            return Store.get('cmsPage')
        },
        
        elysiumCms () {
            return Store.get('elysiumCMS')
        },

        selectedSlides () {
            return Store.get('elysiumCMS').selectedSlides
        },

        selectedSlidesIds() {
            return this.selectedSlides.map(slide => slide.id)
        },

        device () {

            if (this.cmsPage.currentCmsDeviceView === 'tablet-landscape') {
                return 'tablet'
            }

            return this.cmsPage.currentCmsDeviceView
        },

        slidesRepository () {
            return this.repositoryFactory.create('blur_elysium_slides')
        },

        tabs () {
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
                }
            ]
        },
    },

    methods: {

        changeDevice (device: string) {
            this.cmsPage.setCurrentCmsDeviceView(device === 'tablet' ? 'tablet-landscape' : device)
        },

        loadSelectedSlides (selectedSlidesIds: string[]) {
            const criteria = new Criteria()
            criteria.setIds(selectedSlidesIds)

            this.slidesRepository.search(criteria, Context.api).then((result) => {

                /**
                 * filter the selected slides to remove orphaned selections
                 * @todo check if the filter funtion is really needed. Because the selectedSlidesIds are already set in the criteria `criteria.setIds(this.selectedSlidesIds)`
                 */
                const filteredSlides = result.filter((slide) => selectedSlidesIds.includes(slide.id))
                this.selectedSlides = filteredSlides
                this.elysiumCms.setSelectedSlides(filteredSlides)
            }).catch((error) => {
                console.error('Error loading slides', error)
            })
        },

        addSlide (slide: Entity<'blur_elysium_slides'>) {
            this.elysiumCms.addSelectedSlide(slide)
        },

        removeSlide (slide: Entity<'blur_elysium_slides'>) {
            this.elysiumCms.removeSelectedSlide(slide)
        },

        moveUpSlide (slide: Entity<'blur_elysium_slides'>) {
            const currentIndex = this.selectedSlides.indexOf(slide)
            if (currentIndex > 0) this.elysiumCms.moveSelectedSlide(slide, currentIndex, currentIndex - 1)
        },

        moveDownSlide (slide: Entity<'blur_elysium_slides'>) {
            const currentIndex = this.selectedSlides.indexOf(slide)
            if (currentIndex < this.selectedSlides.length - 1) this.elysiumCms.moveSelectedSlide(slide, currentIndex, currentIndex + 1)
        },

        dragSlideDrop (slide: Entity<'blur_elysium_slides'>, fromIndex: number, toIndex: number) {
            this.elysiumCms.moveSelectedSlide(slide, fromIndex, toIndex)
        },
    },

    watch: {
        selectedSlidesIds: {
            handler(slidesIds: string[]) {
                this.element.config.elysiumSlideCollection.value = slidesIds
            },
        }
    },

    created() {
        this.initElementConfig('blur-elysium-slider')
        if (this.element.config.elysiumSlideCollection.value.length > 0) {
            this.loadSelectedSlides(this.element.config.elysiumSlideCollection.value)
        }
    },

    unmounted() {
        this.elysiumCms.clearSelectedSlides()
    }
})