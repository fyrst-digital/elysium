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
            'slide',
            'deviceView'
        ]),

        slideViewportSettings () {
            return this.slide.slideSettings.viewports[this.deviceView]
        },

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
