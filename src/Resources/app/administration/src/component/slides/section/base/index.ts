import template from './template.html.twig'

const { Component, Mixin, Store } = Shopware 

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

        elysiumUI () {
            return Store.get('elysiumUI')
        },

        device () {
            return this.elysiumUI.device
        },

        tabs () {
            return [
                {
                    label: this.$tc('blurElysiumSlides.forms.contentLabel'),
                    name: 'content',
                },
                {
                    label: this.$tc('blurElysiumSlides.forms.slideLinking.label'),
                    name: 'linking',
                }
            ]
        },

        activeTabMeta () {
            return this.tabs.find(tab => tab.name === this.activeTab)
        },

        cardTitle () {
            return this.$tc('blurElysiumSlides.forms.generalTitle')
        }
    }
})
