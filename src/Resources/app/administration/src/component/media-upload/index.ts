import template from './template.html.twig'

const { Component } = Shopware

export default Component.wrapComponentConfig({
    template,

    props: {
        uploadTag: {
            type: String,
            required: true
        },
        media: {
            type: Object,
            required: true
        },
        allowedFile: {
            type: String,
            default: '*/*'
        },
        disabled: {
            type: Boolean
        }
    },

    methods: {
        uploadFinished (payload) {
            this.$emit('upload-finished', payload)
        },

        mediaDropped (payload) {
            this.$emit('media-dropped', payload)
        },

        mediaRemove () {
            this.$emit('media-remove')
        },

        mediaSidebarOpen () {
            this.$emit('media-sidebar-open')
        }
    }
})
