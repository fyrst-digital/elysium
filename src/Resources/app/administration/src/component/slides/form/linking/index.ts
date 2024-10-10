import template from './template.html.twig'

const { Component, Mixin } = Shopware 
const { mapMutations, mapState } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('blur-device-utilities')
    ],

    computed: {

        ...mapState('blurElysiumSlide', [
            'slide'
        ]),

        validateProduct () {
            if (this.slide.slideSettings.slide.linking.type === 'product' && (this.slide.productId === undefined || this.slide.productId === null || this.slide.productId === '')) {
                return {
                    detail: this.$t('blurElysiumSlides.messages.productLinkingMissingEntity'),
                }
            }

            return false
        }
    },

    methods: {

        ...mapMutations('blurElysiumSlide', [
            'setSlide'
        ])
    },
})
