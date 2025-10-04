import template from './template.html.twig'

const { Component, Mixin } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('blur-style-utilities'),
    ],

    data() {
        return {
            styles: {
                container: { 
                    mobile: { 
                        'gap': '16px',
                        'display': 'flex',
                        'flex-direction': 'row',
                        'flex-wrap': 'wrap',
                        'padding': '16px',
                        'background-color': '#f6f6f6',
                        'border-bottom': '1px solid #e6e6e6',
                    },
                },
            },
        };
    },

    computed: {
        slideId () {
            return this.$route.params.id;
        },
        tabs () {
            return [
                {
                    label: this.$t('blurElysiumSlides.settings.baseLabel'),
                    route: () => {
                        return {
                            name: 'blur.elysium.slides.detail.content',
                            params: { id: this.slideId },
                        };
                    },
                },
                {
                    label: this.$t('blurElysiumSlides.settings.mediaLabel'),
                    route: () => {
                        return {
                            name: 'blur.elysium.slides.detail.media',
                            params: { id: this.slideId },
                        };
                    },
                },
                {
                    label: this.$t('blurElysium.general.appearance'),
                    route: () => {
                        return {
                            name: 'blur.elysium.slides.detail.display',
                            params: { id: this.slideId },
                        };
                    },
                },
                {
                    label: this.$t('blurElysiumSlides.settings.advancedLabel'),
                    route: () => {
                        return {
                            name: 'blur.elysium.slides.detail.advanced',
                            params: { id: this.slideId },
                        };
                    },
                }
            ];
        },
    },

    methods: {
        isActive (tab: any) {
            return this.$route.name === tab.route().name;
        }
    }
});
