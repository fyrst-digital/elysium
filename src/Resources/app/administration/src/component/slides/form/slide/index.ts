import template from './template.html.twig'

const { Component, Mixin } = Shopware 
const { mapMutations, mapState } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('blur-device-utilities'),
        Mixin.getByName('blur-style-utilities'),
    ],

    computed: {

        ...mapState('blurElysiumSlide', [
            'slide',
            'currentDevice'
        ]),

        viewportSettings () {
            return this.viewportsSettings[this.currentDevice]
        }
    },

    methods: {

        ...mapMutations('blurElysiumSlide', [
            'setSlide'
        ])
    },

    created () {
        this.viewportsSettings = this.slide.slideSettings.viewports
    }
})
