import template from './template.html.twig'

const { Component, Mixin } = Shopware 

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('blur-device-utilities')
    ],

    data () {
        return {
            activeTab: 'content'
        }
    },

    computed: {
        tabs () {
            return [
                {
                    label: 'Content',
                    description: this.$tc('blurElysiumSlides.forms.displaySlide.description'),
                    name: 'content',
                },
                {
                    label: 'Linking',
                    description: this.$tc('blurElysiumSlides.forms.displayContainer.description'),
                    name: 'linking',
                }
            ]
        },

        activeTabMeta () {
            return this.tabs.find(tab => tab.name === this.activeTab)
        },

        cardTitle () {
            return `General`
        },

        cardDescription () {
            return `Description`
        }
    }
})
