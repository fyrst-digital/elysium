import template from './blur-elysium-slides-create.twig';
import './blur-elysium-slides-create.scss';

const { Component, Mixin } = Shopware;

const { mapPageErrors } = Shopware.Component.getComponentHelper();

const errorConfig = {
    "blur.elysium.slides.create": {
      "elysiumSlides": [
        "label"
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

        // ...mapPageErrors(errorConfig),
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

            /*
            if ( !this.blurElysiumSlides.label ) {
                this.createNotificationError({
                    message: this.$tc('Slide label is required'),
                });

                this.isLoading = false;

                return new Promise((res) => res());
            }
            */

            console.log( 'fire');

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
    }
});