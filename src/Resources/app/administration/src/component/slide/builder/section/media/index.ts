import template from './template.html.twig';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,
    data() {
        return {
            activeTab: 'slideCover',
        };
    },
    computed: {
        tabs() {
            return [
                {
                    label: this.$tc(
                        'blurElysiumSlides.forms.slideCover.label'
                    ),
                    name: 'slideCover',
                },
                {
                    label: this.$tc(
                        'blurElysiumSlides.forms.slideFocusImage.label'
                    ),
                    name: 'focusImage',
                },
            ];
        },
    },
});
