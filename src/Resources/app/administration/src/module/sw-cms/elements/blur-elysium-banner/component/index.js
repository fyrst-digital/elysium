import template from './template.twig'
import './style.scss'

// eslint-disable-next-line no-undef
const { Component, Mixin, Context } = Shopware
const { mapState } = Component.getComponentHelper()

Component.register('blur-cms-el-elysium-banner', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    inject: [
        'repositoryFactory'
    ],

    data () {
        return {
            slide: null
        }
    },

    watch: {
        slideId () {
            this.getSlide()
        }
    },

    computed: {
        ...mapState('cmsPageState', [
            'currentCmsDeviceView'
        ]),

        slideId () {
            if (this.element.config?.elysiumSlide?.value) {
                return this.element.config.elysiumSlide.value
            }

            return ''
        },

        repository () {
            return this.repositoryFactory.create('blur_elysium_slides')
        }
    },

    created () {
        this.createdComponent()
        this.getSlide()
    },

    methods: {
        createdComponent () {
            this.initElementConfig('blur-elysium-banner')
        },

        getSlide () {
            if (this.slideId !== '') {
                this.repository.get(this.slideId, Context.api).then((res) => {
                    this.slide = res
                }).catch((e) => {
                    console.warn(e)
                })
            }
        },

        placeholder (
            property,
            snippet
        ) {
            if (this.slide && this.slide.translated[property]) {
                return this.slide.translated[property]
            }

            return this.$tc(snippet)
        }
    }
})
