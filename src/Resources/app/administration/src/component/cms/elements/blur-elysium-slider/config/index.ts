import template from './template.html.twig'

const { Component, Mixin, Store } = Shopware

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('cms-state'),
        Mixin.getByName('cms-element'),
        // Mixin.getByName('blur-device-utilities')
    ],

    data() {
        return {
            activeTab: 'content'
        }
    },

    computed: {
        cmsPage () {
            return Store.get('cmsPage')
        },

        device () {

            if (this.cmsPage.currentCmsDeviceView === 'tablet-landscape') {
                return 'tablet'
            }

            return this.cmsPage.currentCmsDeviceView
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

        /**
         * @todo since we just looking for at least one slide exist we can not filter orphans at this point
         * move this function to slide-selection component
         */
        selectedSlides: {
            get () {
                return this.element.config.elysiumSlideCollection.value
            },

            set (value) {
                // Note: we are using destructuring assignment syntax here.
                this.element.config.elysiumSlideCollection.value = value
            }
        },
    },

    methods: {

        changeDevice (device: string) {
            this.cmsPage.setCurrentCmsDeviceView(device === 'tablet' ? 'tablet-landscape' : device)
        },

        /**
         * @deprecated since we just looking for at least one slide exist we can not filter orphans at this point
         * move this function to slide-selection component
         */
        filterOrphans (slides) {
            return this.selectedSlides.filter((selectedSlide) => {
                return slides.find((slide) => {
                    return slide.id === selectedSlide
                })
            })
        },
    },

    created() {
        this.initElementConfig('blur-elysium-slider')
    },
})