import template from './template.html.twig'

const { Component, Mixin } = Shopware 
const { mapMutations, mapState, mapGetters } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('placeholder')
    ],

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

        switchDevice (device: string) {
            if (device === "desktop") {
                this.setDeviceView("mobile");
            } else if (device === "mobile") {
                this.setDeviceView("tablet");
            } else if (device === "tablet") {
                this.setDeviceView("desktop");
            }
        }
    },
})
