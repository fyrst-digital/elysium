import template from './template.html.twig';

const { Component, Store } = Shopware;

export default Component.wrapComponentConfig({
    template,

    inject: ['acl'],

    computed: {
        permissionEdit() {
            return this.acl.can('blur_elysium_slides.editor');
        },

        inAppPurchaseCheckout() {
            return Store.get('inAppPurchaseCheckout')
        },

        elysiumProIsActive() {
            return true
        },

        elysiumProFeatures() {
            return [
                { label: 'blurElysiumIAP.features.prioritizedSupport' },
                { label: 'blurElysiumIAP.features.slideBulkEdit' },
                { label: 'blurElysiumIAP.features.reusableSlideTemplates' },
                { label: 'blurElysiumIAP.features.slideInteractivity' },
                { label: 'blurElysiumIAP.features.advancedCmsElements' },
            ];
        }
    },

    methods: {
        openIAPCheckout() {
            try {
                this.inAppPurchaseCheckout.request({ identifier: 'my-iap-identifier' }, 'BlurElysiumSlider')
            } catch (error) {
                console.error('Error during IAP checkout:', error)
            }
        }
    },
});