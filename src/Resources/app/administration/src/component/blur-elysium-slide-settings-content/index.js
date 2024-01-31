import template from './template.html.twig'
import { slides } from '@elysiumSlider/utilities/identifiers'

// eslint-disable-next-line no-undef
const { Component } = Shopware
const { mapMutations, mapState } = Component.getComponentHelper()

export default {
    template,

    computed: {
        ...mapState('blurElysiumSlidesDetail', [
            'slide',
            'loading'
        ]),

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
