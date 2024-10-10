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
            'slide'
        ])
    },

    methods: {

        ...mapMutations('blurElysiumSlide', [
            'setSlide'
        ])
    },
})
