import template from './template.html.twig';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    computed: {
        blurElysiumSectionName() {
            return 'blur-elysium-section';
        },

        elysiumSectionSettingsTitle() {
            if (this.selectedBlock?.customFields?.hasOwnProperty('elysiumBlockSettings')) {
                return this.$tc('blurElysiumSection.sidebar.blockSettingsTitle');
            } else {
                return this.$tc('blurElysiumSection.sidebar.sectionSettingsTitle');
            }
        },

        elysiumSectionSettingsActive() {
            return this.selectedSection?.customFields?.hasOwnProperty('elysiumSectionSettings') ||
                this.selectedBlock?.customFields?.hasOwnProperty('elysiumBlockSettings');
        }
    },
});