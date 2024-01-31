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
            'viewport',
            'loading',
            'acl'
        ]),

        viewportSettings () {
            return this.slide.slideSettings.viewports[this.viewport]
        },

        positionIdentifiers () {
            return slides
        }
    },
    
    methods: {
        ...mapMutations('blurElysiumSlidesDetail', [
            'setSlideSetting',
            'setViewportSetting'
        ]),

        defaultViewportValues (mobileValue, tabletValue, desktopValue) {

            const defaultValues = {
                mobile: mobileValue,
                tablet: tabletValue,
                desktop: desktopValue
            }

            return defaultValues[this.viewport]
        }
    }
}
