import template from './blur-elysium-slides-media-form.twig';

const { Criteria } = Shopware.Data;
const { Component, Context, Mixin } = Shopware;
const { mapPropertyErrors, mapState } = Shopware.Component.getComponentHelper();

const propErrors = [
    'name'
];

Component.register( 'blur-elysium-slides-media-form', {
    template,

    inject: [
        'repositoryFactory',
        'acl',
    ],

    mixins: [
        Mixin.getByName('placeholder'),
    ],

    props: {
        media: {
            type: Object
        },
        mediaSidebar: {
            type: Object
        },
        isLoading: Boolean,
        allowEdit: {
            type: Boolean,
            required: false,
            default: true,
        }
    },

    data() {
        return {
            coverTag: 'blur-elysium-slide-cover-media',
            coverPortraitTag: 'blur-elysium-slide-cover-portrait-media'
        };
    },

    computed: {
        
        ...mapState('blurElysiumSlidesDetail', [
            'slide'
        ]),
        
        ...mapPropertyErrors( 'blurElysiumSlides' , propErrors),


        mediaRepository() {
            return this.repositoryFactory.create('media');
        },
    },

    created() {
        this.getMedia( 'slideCover' )
        this.getMedia( 'slideCoverPortrait' )
    },

    methods: {

        getMedia( key ) {

            this.mediaRepository.get( 
                this.slide[ this.media[key].slideMediaId ], 
                Shopware.Context.api 
            ).then( ( media ) => {
                this.media[key].data = media
            }).catch(( exception ) => {
                console.warn( exception );
            });
        },

        setMedia( id, key ) {

            this.mediaRepository.get( id, Shopware.Context.api )
            .then( ( media ) => {
                this.slide[ this.media[key].slideMediaId ] = id
                this.media[key].data = media
                
            }).catch(( exception ) => {
                console.warn( exception );
            });
        },

        resetMedia( key ) {

            this.slide[ this.media[key].slideMediaId ] = null
            this.media[key].data = null
        }
    }
});