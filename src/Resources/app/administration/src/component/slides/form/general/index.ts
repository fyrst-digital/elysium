import template from './template.html.twig'

const { Component, Mixin, Store } = Shopware 
const { mapMutations, mapState, mapGetters } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('blur-device-utilities'),
        Mixin.getByName('blur-style-utilities')
    ],

    data () {
        return {
            /** @deprecated may be deprected - replaced by slideViewportSettings */
            viewportsSettings: null,
        }
    },

    computed: {

        slide () {
            return Store.get('elysiumSlide').slide
        },

        device () {
            return Store.get('elysiumUI').device
        },

        ...mapState('blurElysiumSlide', [
            // 'slide',
            'currentDevice'
        ]),

        ...mapGetters('error', [
            'getApiError'
        ]),

        nameError () {
            return this.getApiError(this.slide, 'name');
        },

        /** @deprecated may be deprected - replaced by slideViewportSettings */
        viewportSettings () {
            return this.viewportsSettings[this.currentDevice]
        },

        slideViewportSettings () {
            return this.slide.slideSettings.viewports[this.device]
        },
    },

    methods: {

        ...mapMutations('blurElysiumSlide', [
            'setSlide',
            'setCurrentDevice'
        ]),
    },

    created () {
        /** @deprecated may be deprected - replaced by slideViewportSettings */
        this.viewportsSettings = this.slide.slideSettings.viewports
    }
})
