import template from './template.html.twig'

const { Component, Mixin } = Shopware 
const { mapMutations, mapState } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('blur-device-utilities')
    ],

    data () {
        return {
            activeTab: 'slide'
        }
    },

    computed: {
        ...mapState('blurElysiumSlide', [
            'currentDevice'
        ]),

        tabs () {
            return [
                {
                    label: this.$tc('blurElysiumSlides.forms.displaySlide.label'),
                    description: this.$tc('blurElysiumSlides.forms.displaySlide.description'),
                    name: 'slide',
                },
                {
                    label: this.$tc('blurElysiumSlides.forms.displayContainer.label'),
                    description: this.$tc('blurElysiumSlides.forms.displayContainer.description'),
                    name: 'container',
                },
                {
                    label: this.$tc('blurElysiumSlides.forms.displayContent.label'),
                    description: this.$tc('blurElysiumSlides.forms.displayContent.description'),
                    name: 'content',
                },
            ]
        },

        activeTabMeta () {
            return this.tabs.find(tab => tab.name === this.activeTab)
        },

        cardTitle () {
            return `${this.$tc('blurElysium.general.appearance')}: ${this.activeTabMeta.label}`
        },

        cardDescription () {
            return this.activeTabMeta.description
        }
    }
})
