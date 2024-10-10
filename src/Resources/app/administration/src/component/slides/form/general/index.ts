import template from './template.html.twig'

const { Component, Mixin } = Shopware 
const { mapMutations, mapState, mapGetters } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('blur-device-utilities')
    ],

    data () {
        return {
            viewportsSettings: null,
        }
    },

    computed: {

        ...mapState('blurElysiumSlide', [
            'slide',
            'deviceView'
        ]),

        ...mapGetters('error', [
            'getApiError'
        ]),

        nameError () {
            return this.getApiError(this.slide, 'name');
        },

        slideViewportSettings () {
            return this.slide.slideSettings.viewports[this.deviceView]
        },
    },

    methods: {

        ...mapMutations('blurElysiumSlide', [
            'setSlide',
            'setDeviceView'
        ]),
    },

    created () {
        this.viewportsSettings = this.slide.slideSettings.viewports
    }
})
