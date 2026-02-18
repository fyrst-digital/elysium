import template from './template.html.twig';
import './style.scss';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

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
        classes() {
            return [
                `icon-${this.name}`,
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

    created() {
        this.loadIcon(this.name);
    },

    methods: {
        async loadIcon(name: string) {
            const iconUrl = new URL(
                `../../../assets/icons/${name}.svg`,
                import.meta.url
            );
            const response = await fetch(iconUrl.toString());

            console.log(response)

            if (response.ok) {
                this.iconSvgData = await response.text();
            } else {
                console.error(`Icon ${name} not found`);
            }
        },
    },
});