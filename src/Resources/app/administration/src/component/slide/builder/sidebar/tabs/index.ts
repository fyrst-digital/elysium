import template from './template.html.twig'

const { Component, Mixin } = Shopware;

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('blur-style-utilities'),
    ],

    data() {
        return {
            tabConfig: [
                { label: 'blurElysiumSlides.settings.baseLabel', routeSuffix: 'content' },
                { label: 'blurElysiumSlides.settings.mediaLabel', routeSuffix: 'media' },
                { label: 'blurElysium.general.appearance', routeSuffix: 'display' },
                { label: 'blurElysiumSlides.settings.advancedLabel', routeSuffix: 'advanced' },
            ],
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
            return this.tabConfig.map((config) => ({
                label: this.$t(config.label),
                route: () => this.slideId
                    ? { name: `blur.elysium.slides.detail.${config.routeSuffix}`, params: { id: this.slideId } }
                    : { name: `blur.elysium.slides.create.${config.routeSuffix}` },
            }));
        },
    },

    methods: {
        isActive(tab: unknown) {
            const tabObj = tab as { route: () => { name: string } };
            return this.$route.name === tabObj?.route()?.name;
        },
    }
});
