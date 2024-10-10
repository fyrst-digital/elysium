import template from './template.html.twig'

const { Component, Mixin } = Shopware 
const { mapState } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('blur-device-utilities')
    ],

    computed: {

        ...mapState('blurElysiumSlide', [
            'slide',
            'customFieldSet'
        ]),

        hasCustomFields () {
            if (this.customFieldSet.total > 0) {
                return true
            }

            return false
        },
    }
})
