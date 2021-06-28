import template from './blur-elysium-slides-create.twig';
import './blur-elysium-slides-create.scss';

const { Component, Mixin } = Shopware;

const { mapPageErrors } = Shopware.Component.getComponentHelper();

const errorConfig = {
    "blur.elysium.slides.create": {
      "elysiumSlides": [
        "name"
      ]
    }
};

Component.register( 'blur-elysium-slides-create', {
    template,

    inject: [ 
        'repositoryFactory', 
        'acl' 
    ],

    mixins: [
        Mixin.getByName('notification'),
    ],

    data() {
        return {
            blurElysiumSlides: null,
            isSaveSuccessful: false,
            isLoading: false,
            mediaItem: null,
            uploadTag: 'blur-elysium-slides-upload-tag',
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        elysiumSlidesRepository() {
            return this.repositoryFactory.create( 'blur_elysium_slides' );
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {

        createdComponent() {
            Shopware.State.commit('context/resetLanguageToDefault');

            this.blurElysiumSlides = this.elysiumSlidesRepository.create( Shopware.Context.api );
            
            //this.mediaItem = this.mediaRepository.create( Shopware.Context.api );
        },

        saveFinish() {
            this.isSaveSuccessful = false;
            this.$router.push({ name: 'blur.elysium.slides.index' });
        },

        onSave() {
            this.isLoading = true;
            this.isSaveSuccessful = false;

            /*
            if ( !this.blurElysiumSlides.label ) {
                this.createNotificationError({
                    message: this.$tc('Slide label is required'),
                });

                this.isLoading = false;

                return new Promise((res) => res());
            }
            */

            

            return this.elysiumSlidesRepository.save( this.blurElysiumSlides, Shopware.Context.api ).then(() => {
                this.isLoading = false;
                this.isSaveSuccessful = true;
                this.createdComponent();
            }).catch(( exception ) => {
                this.createNotificationError({
                    message: this.$tc('sw-customer.detail.messageSaveError'),
                });
                this.isLoading = false;
            });
        },

        onCancel() {
            this.$router.push({ name: 'blur.elysium.slides.index' });
        },

        /*
        setMediaItem({ targetId }) {
            this.mediaRepository.get(targetId)
            .then((updatedMedia) => {
                this.mediaItem = updatedMedia;
                this.blurElysiumSlides.mediaId = targetId;
            });
        },
        */

        setMediaItem( media ) {
            let mediaId = media.targetId ? media.targetId : media.id;
            
            this.mediaRepository.get( mediaId, Shopware.Context.api )
            .then( ( updatedMedia ) => {
                this.mediaItem = updatedMedia;
                this.blurElysiumSlides.mediaId = mediaId;
            }).catch(( exception ) => {
                console.warn( exception );
            });
        },

        onDropMedia(mediaItem) {
            this.setMediaItem( mediaItem );
        },

        onOpenMedia() {
            this.$refs.mediaSidebarItem.openContent();
        },

        onUnlinkMedia() {
            this.mediaItem = null;
            this.blurElysiumSlides.mediaId = null;
        },

        setMediaFromSidebar(mediaEntity) {
            this.mediaItem = mediaEntity;
            this.blurElysiumSlides.mediaId = mediaEntity.id;
        },
    }
});