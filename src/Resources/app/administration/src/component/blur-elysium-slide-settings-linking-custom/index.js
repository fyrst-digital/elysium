import template from './template.html.twig'
import { slides } from '@elysiumSlider/utilities/identifiers'

// eslint-disable-next-line no-undef
const { Component, Mixin } = Shopware
const { mapState } = Component.getComponentHelper()

export default {
    template,

    mixins: [
        Mixin.getByName('blur-editable')
    ],

    computed: {
        ...mapState('blurElysiumSlidesDetail', [
            'slide',
            'loading',
            'acl'
        ]),

        positionIdentifiers () {
            return slides
        }
    }

    /**
     * @deprecated
    methods: {
        ...mapMutations('blurElysiumSlidesDetail', [
            'setSlideSetting',
            'setViewportSetting'
        ])
    }
    */
}
