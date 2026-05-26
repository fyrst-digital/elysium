import template from './template.html.twig';
import {
    buttonColors,
    buttonSizes,
} from '@elysium/component/utilities/settings/buttons';

const { Component, Mixin, Store, Data } = Shopware;
const { Criteria } = Data;

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('blur-style-utilities'),
    ],

    computed: {
        slide() {
            return Store.get('elysiumSlide').slide;
        },

        validateProduct() {
            if (
                this.slide.slideSettings.slide.linking.type === 'product' &&
                [undefined, null, ''].includes(this.slide.productId)
            ) {
                return {
                    detail: this.$t(
                        'blurElysiumSlides.messages.productLinkingMissingEntity'
                    ),
                };
            }

            return false;
        },

        validateCategory() {
            if (
                this.slide.slideSettings.slide.linking.type === 'category' &&
                [undefined, null, ''].includes(this.slide.categoryId)
            ) {
                return {
                    detail: this.$t(
                        'blurElysiumSlides.messages.categoryLinkingMissingEntity'
                    ),
                };
            }

            return false;
        },

        buttonColors() {
            return buttonColors.map((color) => {
                return {
                    value: color.value,
                    label: this.$tc(color.label),
                };
            });
        },

        buttonSizes() {
            return buttonSizes.map((size) => {
                return {
                    value: size.value,
                    label: this.$tc(size.label),
                };
            });
        },

        productCriteria() {
            const criteria = new Criteria(1, 25);
            criteria.addFilter(Criteria.equals('active', true));
            return criteria;
        },
    },
});
