import template from './template.html.twig'
import { buttonColors, buttonSizes } from 'blurElysium/component/utilities/settings/buttons'

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
        },

        buttonColors () {
            return buttonColors.map((color) => {
                return {
                    value: color.value,
                    label: this.$tc(color.label)
                }
            })
        },

        buttonSizes () {
            return buttonSizes.map((size) => {
                return {
                    value: size.value,
                    label: this.$tc(size.label)
                }
            })
        }
    },

    methods: {

        ...mapMutations('blurElysiumSlide', [
            'setSlide'
        ])
    },
})
