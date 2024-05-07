import template from './template.html.twig'

const { Component } = Shopware

export default Component.wrapComponentConfig({

    template,

    inject: [
        'feature',
    ],

    props: {
        name: {
            type: String,
            required: true,
        },
        color: {
            type: String,
            required: false,
            default: null,
        },
        small: {
            type: Boolean,
            required: false,
            default: false,
        },
        large: {
            type: Boolean,
            required: false,
            default: false,
        },
        size: {
            type: String,
            required: false,
            default: null,
        },
        decorative: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    data() {
        return {
            iconSvgData: '',
        };
    },

    computed: {
        iconName() {
            return `icons-${this.name}`;
        },

        classes() {
            return [
                `icon--${this.name}`,
                {
                    'sw-icon--small': this.small,
                    'sw-icon--large': this.large,
                },
            ];
        },

        styles() {
            let size = this.size;

            if (!Number.isNaN(parseFloat(size)) && !Number.isNaN(size - 0)) {
                size = `${size}px`;
            }

            return {
                color: this.color,
                width: size,
                height: size,
            };
        },
    },

    beforeMount() {
        this.iconSvgData = `<svg id="meteor-icon-kit__${this.name}"></svg>`
    },

    watch: {
        name: {
            handler(newName) {
                if (!newName) {
                    return;
                }

                const [variant, ...iconName] = newName.split('-');
                this.loadIconSvgData(variant, iconName.join('-'), newName);
            },
            immediate: true,
        },
    },

    methods: {
        loadIconSvgData (variant: string, iconName: string, iconFullName: string) {
            console.log("meddl");
            return import(`blurElysium/icons/${variant}/${iconName}.svg`).then((iconSvgData) => {

                if (iconSvgData.default) {
                    this.iconSvgData = iconSvgData.default;
                } else {
                    // note this only happens if the import exists but does not export a default
                    console.error(`The SVG file for the icon name ${iconFullName} could not be found and loaded.`);
                    this.iconSvgData = '';
                }
            });
        }
    }
});