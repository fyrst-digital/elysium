import template from './blur-elysium-slides-media-form.twig';
import { slides } from "@elysiumSlider/utilities/identifiers";

const { Component, Mixin } = Shopware;
const { mapState, mapMutations } = Component.getComponentHelper();

export default {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('blur-editable'),
    ],

    data() {
        return {
            uploadTag: {
                cover: 'blur-elysium-slide-cover-media',
                coverPortrait: 'blur-elysium-slide-cover-portrait-media'
            },
            mediaTypes: {
                slideCover: {
                    idField: 'mediaId',
                    objectField: 'media'
                },
                slideCoverPortrait: {
                    idField: 'mediaPortraitId',
                    objectField: 'mediaPortrait'
                }
            }
        };
    },

    computed: {
        
        ...mapState('blurElysiumSlidesDetail', [
            'slide',
            'media',
            'mediaSidebar',
            'acl'
        ]),

        slideCoverPreview() {
            if (this.slide.media) {
                return this.slide.media
            } else if(this.media.slideCover) {
                return this.media.slideCover
            }

            return null
        },

        slideCoverPortraitPreview() {
            if (this.slide.mediaPortrait) {
                return this.slide.mediaPortrait
            } else if(this.media.slideCoverPortrait) {
                return this.media.slideCoverPortrait
            }
            
            return null
        },

        positionIdentifiers() {
            return slides
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        entityName() {
            return 'blur_elysium_slides'
        },
    },

    methods: {
        ...mapMutations('blurElysiumSlidesDetail', [
            'setSlideProperty',
            'setSlideMedia'
        ]),

        fetchMedia( type ) {
            this.mediaRepository.get( 
                this.slide[this.mediaTypes[type].idField], 
                Shopware.Context.api 
            ).then( ( media ) => {
                this.setSlideMedia({
                    key: type, 
                    value: media
                })
            }).catch(( exception ) => {
                console.error( exception )
            })
        },

        setMedia( type, id ) {
            this.setSlideProperty({
                key: this.mediaTypes[type].idField, 
                value: id
            })
            
            this.fetchMedia( type )
        },

        resetMedia( type ) {
            this.setSlideProperty({
                key: this.mediaTypes[type].idField, 
                value: null
            })
            this.setSlideProperty({
                key: this.mediaTypes[type].objectField, 
                value: null
            })
            this.setSlideMedia({
                key: this.mediaTypes[type], 
                value: null
            })
        },

        changeCoverType( value ) {
            this.coverType = value
        }
    }
}