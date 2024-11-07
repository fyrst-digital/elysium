import template from './template.html.twig'

const { Component, Mixin } = Shopware 
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
            viewportsSettings: null,
        }
    },

    computed: {

        ...mapState('blurElysiumSlide', [
            'slide',
            'currentDevice'
        ]),

        ...mapGetters('error', [
            'getApiError'
        ]),

        nameError () {
            return this.getApiError(this.slide, 'name');
        },

        slideViewportSettings () {
            return this.slide.slideSettings.viewports[this.currentDevice]
        },
    },

    methods: {

        ...mapMutations('blurElysiumSlide', [
            'setSlide',
            'setCurrentDevice'
        ]),
    },

    created () {
        this.viewportsSettings = this.slide.slideSettings.viewports
    }
})
