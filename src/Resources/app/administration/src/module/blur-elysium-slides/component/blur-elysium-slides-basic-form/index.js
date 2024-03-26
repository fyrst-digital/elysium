import template from './blur-elysium-slides-basic-form.twig'
import { slides } from '@elysiumSlider/utilities/identifiers'

// eslint-disable-next-line no-undef
const { Component, Mixin } = Shopware
const { mapMutations, mapPropertyErrors, mapState } = Component.getComponentHelper()

const propErrors = [
    'name'
]

export default {
    template,

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('blur-editable')
    ],

    /**
     * @deprecated this props will be removed
     * isLoading and allowEdit will be handled through the ACL vuex state
     */
    props: {
        isLoading: {
            type: Boolean
        },
        allowEdit: {
            type: Boolean,
            required: false,
            default: true
        }
    },

    computed: {

        ...mapState('blurElysiumSlidesDetail', [
            'slide',
            'viewport',
            'loading',
            'acl'
        ]),

        ...mapPropertyErrors('blurElysiumSlides', propErrors),

        positionIdentifiers () {
            return slides
        },

        viewportSettings () {
            return this.slide.slideSettings.viewports[this.viewport]
        },

        urlOverlayActive () {
            return !!(this.slide.slideSettings && this.slide.slideSettings.urlOverlay)
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
