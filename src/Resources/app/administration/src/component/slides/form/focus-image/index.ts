import template from './template.html.twig'

const { Component, Mixin } = Shopware 
const { mapMutations, mapState } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('blur-media')
    ],

    inject: [
        'repositoryFactory',
        'acl'
    ],

    computed: {

        ...mapState('blurElysiumSlide', [
            'slide',
            'mediaSidebar',
            'deviceView'
        ]),

        focusImageMedia() {
            if (this.slide.presentationMedia) {
                return this.slide.presentationMedia
            }

            return null            
        },

        permissionView() {
            return this.acl.can('blur_elysium_slides.viewer')
        },

        permissionCreate() {
            return this.acl.can('blur_elysium_slides.creator')
        },

        permissionEdit() {
            return this.acl.can('blur_elysium_slides.editor')
        },

        permissionDelete() {
            return this.acl.can('blur_elysium_slides.deleter')
        },
    },

    methods: {

        ...mapMutations('blurElysiumSlide', [
            'setSlideProperty'
        ]),
    },

    watch: {
        'slide.presentationMediaId'(value: string | null) {
            if (value !== null) {
                this.fetchMedia(value, 'presentationMedia')
            }
        },
    },
})
