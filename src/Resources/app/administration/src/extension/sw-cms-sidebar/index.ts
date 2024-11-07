import template from './template.html.twig'

// eslint-disable-next-line no-undef
const { Component, Feature } = Shopware

Component.override('sw-cms-sidebar', {
    template,

    computed: {
        isSectionFeatureActive () {
            return Feature.isActive('BLUR_ELYSIUM_CMS_SECTION') ?? false
        },

        elysiumSectionSettingsTitle() {
            if (this.selectedBlock !== null) {
                return this.$tc('blurElysiumSection.sidebar.blockSettingsTitle')
            } else if (this.selectedSection !== null) {
                return this.$tc('blurElysiumSection.sidebar.sectionSettingsTitle')
            }
        },

        elysiumSectionSettingsActive() {
            return this.selectedBlock?.customFields?.hasOwnProperty('elysiumBlockSettings') || this.selectedSection?.customFields?.hasOwnProperty('elysiumSectionSettings')
        },
    }
})
