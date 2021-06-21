import template from './blur-elysium-slides-create.twig';
import './blur-elysium-slides-create.scss';

const { Component, Mixin } = Shopware;

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
    },

    created() {
        this.createdComponent();
    },

    methods: {

        createdComponent() {
            Shopware.State.commit('context/resetLanguageToDefault');

            this.blurElysiumSlides = this.elysiumSlidesRepository.create( Shopware.Context.api );
        },

        saveFinish() {
            this.isSaveSuccessful = false;
            this.$router.push({ name: 'blur.elysium.slides.index' });
        },

        onSave() {
            this.isLoading = true;
            this.isSaveSuccessful = false;

            console.dir( this.blurElysiumSlides );

            return this.elysiumSlidesRepository.save( this.blurElysiumSlides, Shopware.Context.api ).then(() => {
                this.isLoading = false;
                this.isSaveSuccessful = true;
            }).catch(() => {
                this.createNotificationError({
                    message: this.$tc('sw-customer.detail.messageSaveError'),
                });
                this.isLoading = false;
            });
        },

        onCancel() {
            this.$router.push({ name: 'blur.elysium.slides.index' });
        },
    }
});