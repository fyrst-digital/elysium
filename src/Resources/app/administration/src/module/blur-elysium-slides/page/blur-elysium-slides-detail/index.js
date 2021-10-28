import template from './blur-elysium-slides-detail.twig';
import './blur-elysium-slides-detail.scss';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;
const { mapPageErrors } = Shopware.Component.getComponentHelper();

Component.register( 'blur-elysium-slides-detail', {
    template,

    inject: [
        'repositoryFactory',
        'acl',
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('discard-detail-page-changes')('blur_elysium_slides'),
        Mixin.getByName('placeholder'),
    ],

    props: {
        blurElysiumSlideId: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            entityName: 'blur_elysium_slides',
            isLoading: false,
            isSaveSuccessful: false,
            blurElysiumSlide: null,
            mediaItem: null,
            customFieldSets: [],
            uploadTag: 'blur-elysium-slide-upload-tag'
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle( this.identifier ),
        };
    },

    computed: {
        identifier() {
            return this.slideLabel;
        },

        slideLabel() {

            if (!this.$i18n) {
                return '';
            }

            // return name
            return this.placeholder(this.blurElysiumSlide, 'name', this.$tc('sw-product.detail.textHeadline'));
        },

        elysiumSlidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        defaultCriteria() {
            const criteria = new Criteria();

            return criteria;
        },

        customFieldSetRepository() {
            return this.repositoryFactory.create( 'custom_field_set' );
        },
    
        customFieldSetCriteria() {
            const criteria = new Criteria();
    
            criteria.addFilter(
                Criteria.equals( 'relations.entityName', this.entityName )
            );
    
            criteria.getAssociation( 'customFields' )
                    .addSorting(Criteria.sort('config.customFieldPosition'));

    
            return criteria;
        },

        editMode: {
            get() {
                if (typeof this.$route.query.edit === 'boolean') {
                    return this.$route.query.edit;
                }

                return this.$route.query.edit === 'true';
            },
            set(editMode) {
                this.$router.push({ name: this.$route.name, query: { edit: editMode } });
            },
        },
    },

    watch: {
        blurElysiumSlideId() {
            this.createdComponent();
        },
    },

    created() {
        this.createdComponent();
        this.loadCustomFieldSets();
    },

    methods: {

        createdComponent() {
            
            this.isLoading = true;

            this.elysiumSlidesRepository.get(
                this.blurElysiumSlideId,
                Shopware.Context.api,
                this.defaultCriteria,
            ).then((blurElysiumSlide) => {
                this.blurElysiumSlide = blurElysiumSlide;

                if ( blurElysiumSlide.mediaId ) {
                    this.setMediaItem( blurElysiumSlide.mediaId );
                }
                
                this.isLoading = false;
            });
        },

        setMediaItem( mediaId ) {

            this.mediaRepository.get( mediaId, Shopware.Context.api )
            .then( ( updatedMedia ) => {
                this.mediaItem = updatedMedia;
                this.blurElysiumSlide.mediaId = mediaId;
            }).catch(( exception ) => {
                console.warn( exception );
            });
        },

        initState() {
            Shopware.State.commit('blurElysiumSlidesDetail/setApiContext', Shopware.Context.api);



            // when product exists
            if ( this.blurElysiumSlideId ) {
                return this.loadState();
            }

            // When no product id exists init state and new product with the repositoryFactory
            return this.createState().then(() => {
                // create new product number
                this.numberRangeService.reserve('product', '', true).then((response) => {
                    this.productNumberPreview = response.number;
                    this.product.productNumber = response.number;
                });
            });
        },


        loadCustomFieldSets() {

            this.customFieldSetRepository.search( this.customFieldSetCriteria, Shopware.Context.api )
            .then( ( result ) => {
                this.customFieldSets = result.filter((set) => set.customFields.length > 0);
                // this.customFieldSets = result;
                this.isLoading = false;
            }, reason => {
                this.customFieldSets = [];
            });
        },

        loadState() {
            Shopware.State.commit('blurElysiumSlidesDetail/setLocalMode', false);
            Shopware.State.commit('blurElysiumSlidesDetail/setBlurElysiumSlideIdId', this.blurElysiumSlideId);
            Shopware.State.commit('shopwareApps/setSelectedIds', [this.blurElysiumSlideId]);
            return this.loadAll();
        },

        loadAll() {
            return Promise.all([
                this.loadSlide()
            ]);
        },

        loadSlide() {
            Shopware.State.commit('blurElysiumSlidesDetail/setLoading', ['blurElysiumSlide', true]);

            this.elysiumSlidesRepository.get(
                this.blurElysiumSlideId,
                Shopware.Context.api,
                this.defaultCriteria,
            ).then((res) => {
                this.blurElysiumSlide = res;
                Shopware.State.commit('blurElysiumSlidesDetail/setBlurElysiumSlide', res);
                Shopware.State.commit('blurElysiumSlidesDetail/setLoading', ['blurElysiumSlide', false]);
            });
        },

        saveFinish() {
            this.isSaveSuccessful = false;
            this.editMode = false;
        },

        async onSave() {
            this.isLoading = true;

            /*
            if (!this.editMode) {
                return false;
            }
            */

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

            return this.elysiumSlidesRepository.save( this.blurElysiumSlide, Shopware.Context.api ).then(() => {
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

        saveOnLanguageChange() {
            return this.onSave();
        },

        abortOnLanguageChange() {
            return this.elysiumSlidesRepository.hasChanges(this.blurElysiumSlide);
        },

        onChangeLanguage(languageId) {
            Shopware.State.commit('context/setApiLanguageId', languageId);
            this.initState();
        },

        onActivateCustomerEditMode() {
            this.editMode = true;
        },

        onAbortButtonClick() {
            this.discardChanges();
            this.editMode = false;
        },

        onDropMedia(mediaItem) {
            this.setMediaItem( mediaItem.id );
        },

        onOpenMedia() {
            this.$refs.mediaSidebarItem.openContent();
        },

        onUnlinkMedia() {
            this.mediaItem = null;
            this.blurElysiumSlide.mediaId = null;
        },

        setMediaFromSidebar(mediaEntity) {
            this.mediaItem = mediaEntity;
            this.blurElysiumSlide.mediaId = mediaEntity.id;
        },
    }
});