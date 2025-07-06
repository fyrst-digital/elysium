import template from './template.html.twig';

const { Component } = Shopware;

export default Component.wrapComponentConfig({
    template,

    props: {
        title: {
            type: [String, Boolean],
            default: false,
        },
        description: {
            type: [String, Boolean],
            default: false,
        },
        aspectRatioX: {
            type: Number,
            default: 16,
        },
        aspectRatioY: {
            type: Number,
            default: 9,
        },
        maxHeight: {
            type: String,
            default: 'none',
        },
    },

    computed: {
        wrapperStyles() {
            return {
                display: 'flex',
                flexDirection: 'row',
                flexWrap: 'wrap',
                justifyContent: 'center',
                alignItems: 'center',
                background: 'none #f9f9f9',
                border: '2px dashed #dddddd',
                borderRadius: '10px',
                padding: '50px 30px',
                aspectRatio: `${this.aspectRatioX} / ${this.aspectRatioY}`,
                maxHeight: this.maxHeight,
                position: 'relative'
            }
        },

        containerStyles() {
            return {
                display: 'flex',
                flexDirection: 'column',
                flexWrap: 'wrap',
                gap: '20px',
                textAlign: 'center',
                maxWidth: '600px'
            }
        },

        titleStyles() {
            return {
                fontWeight: 600,
                fontSize: '24px'
            }
        },

        descriptionStyles() {
            return {
                lineHeight: 1.5,
                fontSize: '16px'
            }
        }
    }
});
