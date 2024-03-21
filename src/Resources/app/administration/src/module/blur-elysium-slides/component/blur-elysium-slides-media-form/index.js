import template from './blur-elysium-slides-media-form.twig'
import { slides } from '@elysiumSlider/utilities/identifiers'

// eslint-disable-next-line no-undef
const { Component, Mixin, Context } = Shopware
const { mapState, mapMutations } = Component.getComponentHelper()

export default {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('blur-editable'),
        Mixin.getByName('notification')
    ],

    data () {
        return {
            uploadTag: {
                slideCover: 'blur-elysium-slide-cover',
                slideCoverMobile: 'blur-elysium-slide-cover-mobile',
                slideCoverTablet: 'blur-elysium-slide-cover-tablet',
                slideCoverVideo: 'blur-elysium-slide-cover-video',
                coverPortrait: 'blur-elysium-slide-cover-portrait-media',
                presentationMedia: 'blur-elysium-slide-presentation-media'
            }
        }
    },

    watch: {
        slide: {
            handler: function(slide) {
                console.log('watch slide', slide)
            },
            deep: true
        }
    },

    computed: {
        ...mapState('blurElysiumSlidesDetail', [
            'slide',
            'mediaSidebar',
            'viewport',
            'acl'
        ]),

        slideCover () {
            if (this.slide.slideCover) {
                return this.slide.slideCover
            }

            return null
        },

        slideCoverMobile () {
            if (this.slide.slideCoverMobile) {
                return this.slide.slideCoverMobile
            }

            return null
        },

        slideCoverTablet () {
            if (this.slide.slideCoverTablet) {
                return this.slide.slideCoverTablet
            }

            return null
        },

        slideCoverVideo () {
            if (this.slide.slideCoverVideo) {
                return this.slide.slideCoverVideo
            }

            return null
        },

        presentationMedia () {
            if (this.slide.presentationMedia) {
                return this.slide.presentationMedia
            }

            return null
        },

        viewportSettings () {
            return this.slide.slideSettings.viewports[this.viewport]
        },

        positionIdentifiers () {
            return slides
        },

        mediaRepository () {
            return this.repositoryFactory.create('media')
        },

        entityName () {
            return 'blur_elysium_slides'
        }
    },

    methods: {
        ...mapMutations('blurElysiumSlidesDetail', [
            'setSlideProperty',
            'setViewportSetting'
        ]),

        fetchMedia (propertyId, property) {
            this.mediaRepository.get(
                this.slide[propertyId],
                Context.api
            ).then((media) => {
                this.setSlideProperty({
                    key: property,
                    value: media
                })
            }).catch((exception) => {
                console.error(exception)
            })
        },

        setMedia (property, media, allowedType = null) {
            if (media.mimeType.split('/')[0] !== allowedType) {
                let mediaTypeSnippet = this.$tc(`blurElysiumSlides.mediaType.${allowedType}`)
                this.createNotificationWarning({
                    title: this.$t('blurElysiumSlides.messages.mediaTypeNotAllowedTitle'),
                    message: this.$t('blurElysiumSlides.messages.mediaTypeNotAllowed', { snippet: mediaTypeSnippet })
                })
                console.warn('media type not allowed')
                return
            }

            let propertyId = `${property}Id`
            
            this.setSlideProperty({
                key: propertyId,
                value: media.id
            })

            this.fetchMedia(propertyId, property)
        },

        resetMedia (property) {
            let propertyId = `${property}Id`

            this.setSlideProperty({
                key: property,
                value: null
            })
            this.setSlideProperty({
                key: propertyId,
                value: null
            })
        }
    }
}
