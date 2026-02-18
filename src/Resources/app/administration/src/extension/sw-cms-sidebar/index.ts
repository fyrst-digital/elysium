import template from './template.html.twig';
import './style.scss';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    computed: {
        blurElysiumSectionName() {
            return 'blur-elysium-section';
        },

        elysiumSectionSettingsTitle() {
            if (Object.hasOwn(this.selectedBlock?.customFields ?? {}, 'elysiumBlockSettings')) {
                return this.$tc('blurElysiumSection.sidebar.blockSettingsTitle');
            } else {
                return this.$tc('blurElysiumSection.sidebar.sectionSettingsTitle');
            }
        },

        elysiumSectionSettingsActive() {
            return Object.hasOwn(this.selectedSection?.customFields ?? {}, 'elysiumSectionSettings') ||
                Object.hasOwn(this.selectedBlock?.customFields ?? {}, 'elysiumBlockSettings');
        }
    },
});