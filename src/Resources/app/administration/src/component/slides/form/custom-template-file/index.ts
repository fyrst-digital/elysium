import template from './template.html.twig'

const { Component, Mixin } = Shopware 
const { mapMutations, mapState } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('placeholder')
    ],

    computed: {

        ...mapState('blurElysiumSlide', [
            'slide'
        ]),
    },

    methods: {

        ...mapMutations('blurElysiumSlide', [
            'setSlide'
        ]),
    },
})
