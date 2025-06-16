import template from './template.html.twig';
import './style.scss';

const { Component, Store } = Shopware;

export default Component.wrapComponentConfig({
    template,

    props: {
        showLabel: {
            type: Boolean,
            default: true,
        },

        layout: {
            type: String,
            default: 'column', // 'column' | 'row'
        },

        device: {
            type: String,
            default: 'desktop',
        },
    },

    data() {
        return {
            viewports: [
                {
                    name: 'mobile',
                    icon: 'blurph-device-mobile',
                    label: this.$tc('blurElysium.device.phone'),
                },
                {
                    name: 'tablet',
                    icon: 'blurph-device-tablet',
                    label: this.$tc('blurElysium.device.tablet'),
                },
                {
                    name: 'desktop',
                    icon: 'blurph-device-desktop',
                    label: this.$tc('blurElysium.device.desktop'),
                },
            ],
        };
    },

    methods: {
        changeViewport(viewport: string) {
            this.$emit('change-device', viewport);
        },
    },
});
