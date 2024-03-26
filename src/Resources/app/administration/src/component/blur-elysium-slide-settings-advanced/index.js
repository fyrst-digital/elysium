import template from './template.html.twig'
import { slides } from '@elysiumSlider/utilities/identifiers'

// eslint-disable-next-line no-undef
const { Component, Mixin } = Shopware
const { mapMutations, mapState } = Component.getComponentHelper()

export default {
    template,

    mixins: [
        Mixin.getByName('blur-editable')
    ],

    computed: {
        ...mapState('blurElysiumSlidesDetail', [
            'slide',
            'customFieldSets',
            'loading',
            'acl'
        ]),

        hasCustomFields () {
            if (this.customFieldSets.total > 0 && this.customFieldSets.first().customFields.length > 0) {
                return true
            }

            return false
        },

        positionIdentifiers () {
            return slides
        }
    },

    methods: {
        ...mapMutations('blurElysiumSlidesDetail', [
            'setSlideSetting'
        ])
    }
}
