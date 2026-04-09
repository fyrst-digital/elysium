import template from './template.html.twig';

const { Component, Mixin, Store } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [Mixin.getByName('blur-device-utilities')],

    data() {
        return {
            activeTab: 'content',
        };
    },

    computed: {
        slide() {
            return Store.get('elysiumSlide').slide;
        },

        elysiumUI() {
            return Store.get('elysiumUI');
        },

        device() {
            return this.elysiumUI.device;
        },

        tabs() {
            return [
                {
                    label: this.$tc('blurElysiumSlides.forms.contentLabel'),
                    name: 'content',
                    hasError: this.contentError,
                },
                {
                    label: this.$tc(
                        'blurElysiumSlides.forms.slideLinking.label'
                    ),
                    name: 'linking',
                },
            ];
        },

        slideErrors() {
            return Store.get('error').getErrorsForEntity(
                'blur_elysium_slides',
                this.slide.id
            );
        },

        activeTabMeta() {
            return this.tabs.find((tab) => tab.name === this.activeTab);
        },

        cardTitle() {
            return this.$tc('blurElysiumSlides.forms.generalTitle');
        },

        contentError() {
            console.log(this.slideErrors?.hasOwnProperty('name'));
            return Boolean(this.slideErrors?.hasOwnProperty('name')) || Boolean(this.slideErrors?.hasOwnProperty('activeFrom'));
        }
    },
});
