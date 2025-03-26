import template from './template.html.twig'
import { buttonColors, buttonSizes } from '@elysium/component/utilities/settings/buttons'

const { Component, Mixin, Store } = Shopware 

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('placeholder')
    ],

    computed: {

        slide () {
            return Store.get('elysiumSlide').slide
        },

        validateProduct () {
            if (
                this.slide.slideSettings.slide.linking.type === 'product' &&
                [undefined, null, ''].includes(this.slide.productId)
            ) {
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
})
