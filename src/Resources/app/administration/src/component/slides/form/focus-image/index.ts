import template from './template.html.twig'

const { Component, Mixin, Context } = Shopware 
const { mapMutations, mapState } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('blur-media')
    ],

    inject: [
        'repositoryFactory'
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
        }
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
    }
})
