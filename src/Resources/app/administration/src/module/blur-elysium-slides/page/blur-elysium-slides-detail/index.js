import template from './blur-elysium-slides-detail.twig';
import './blur-elysium-slides-detail.scss';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;
const { mapPageErrors, mapState } = Shopware.Component.getComponentHelper();

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
            required: false,
            default: null
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
            detailRoute: 'blur.elysium.slides.detail',
            createRoute: 'blur.elysium.slides.create',
            uploadTag: 'blur-elysium-slide-upload-tag'
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle( this.identifier ),
        };
    },

    computed: {

        ...mapState('blurElysiumSlidesDetail', [
            'slide'
        ]),

        identifier() {
            return this.slideLabel;
        },

        currentRoute() {
            return this.$route.name;
        },

        slideLabel() {

            if (!this.$i18n) {
                return '';
            }

            // return name
            return this.placeholder(this.slide, 'name', this.$tc('BlurElysiumSlides.actions.newSlide') );
        },

        elysiumSlidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        },

        elysiumSlideCreate() {
            return this.elysiumSlidesRepository.create( Shopware.Context.api );
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

    beforeCreate() {
        /** 
         * @TODO outsource config object
        **/
        Shopware.State.registerModule( 'blurElysiumSlidesDetail', {
            namespaced: true,
            state() {
                return {
                    apiContext: {},
                    slide: {},
                    localMode: false,
                    loading: {
                        slide: false
                    }
                }
            },
            mutations: {
                setApiContext(state, apiContext) {
                    state.apiContext = apiContext;
                },
                setSlide(state, newSlide) {
                    state.slide = newSlide;
                },
                setLocalMode(state, value) {
                    state.localMode = value;
                },
                setLoading(state, value) {
                    const name = value[0];
                    const data = value[1];
        
                    if (typeof data !== 'boolean') {
                        return false;
                    }
        
                    if (state.loading[name] !== undefined) {
                        state.loading[name] = data;
                        return true;
                    }
                    return false;
                },
            }
        });
    },

    created() {
        this.createdComponent();
        this.loadCustomFieldSets();
    },

    methods: {

        createdComponent() {
            
            if ( this.currentRoute === this.createRoute ) {
                /**
                 * reset admin language to default
                 * if create new slide
                **/
                Shopware.State.commit('context/resetLanguageToDefault');
            }

            this.isLoading = true;

            if ( this.blurElysiumSlideId !== null ) {

                /**
                 * if slide id is provided load the according slide
                **/
                this.elysiumSlidesRepository.get(
                    this.blurElysiumSlideId,
                    Shopware.Context.api,
                    this.defaultCriteria,
                ).then((blurElysiumSlide) => {
                    this.blurElysiumSlide = blurElysiumSlide;
                    Shopware.State.commit('blurElysiumSlidesDetail/setSlide', blurElysiumSlide);
                    
                    if ( blurElysiumSlide.mediaId ) {
                        this.setMediaItem( blurElysiumSlide.mediaId );
                    }

                    this.isLoading = false;
                });                
            } else {
                /**
                 * if slide id is not provided go here
                **/
                Shopware.State.commit('blurElysiumSlidesDetail/setSlide', this.elysiumSlideCreate);
            }

        },

        setMediaItem( mediaId ) {

            this.mediaRepository.get( mediaId, Shopware.Context.api )
            .then( ( updatedMedia ) => {

                this.mediaItem = updatedMedia;

                if ( this.blurElysiumSlide !== null ) {
                    this.blurElysiumSlide.mediaId = mediaId;
                } else {
                    this.elysiumSlideCreate.mediaId = mediaId;
                }
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
                // this.numberRangeService.reserve('product', '', true).then((response) => {
                //    this.productNumberPreview = response.number;
                //    this.product.productNumber = response.number;
                // });
            });
        },

        createState() {
            Shopware.State.commit('blurElysiumSlidesDetail/setSlide', this.elysiumSlideCreate);
        },

        loadCustomFieldSets() {

            this.customFieldSetRepository.search( this.customFieldSetCriteria, Shopware.Context.api )
            .then( ( result ) => {
                this.customFieldSets = result.filter((set) => set.customFields.length > 0);
                this.isLoading = false;
            }, reason => {
                this.customFieldSets = [];
            });
        },

        loadState() {
            Shopware.State.commit('blurElysiumSlidesDetail/setLocalMode', false);
            Shopware.State.commit('blurElysiumSlidesDetail/setSlide', this.blurElysiumSlide);
            return this.loadAll();
        },

        loadAll() {
            return Promise.all([
                this.loadSlide()
            ]);
        },

        loadSlide() {
            Shopware.State.commit('blurElysiumSlidesDetail/setLoading', ['slide', true]);

            this.elysiumSlidesRepository.get(
                this.blurElysiumSlideId,
                Shopware.Context.api,
                this.defaultCriteria,
            ).then((res) => {
                this.blurElysiumSlide = res;
                Shopware.State.commit('blurElysiumSlidesDetail/setSlide', res);
                Shopware.State.commit('blurElysiumSlidesDetail/setLoading', ['slide', false]);
            });
        },

        saveFinish() {
            this.isSaveSuccessful = false;
            this.editMode = false;
        },

        async onSave() {
            this.isLoading = true;
            this.isSaveSuccessful = false;

            if ( !( this.blurElysiumSlide === null || this.blurElysiumSlide === undefined ) ) {

                // save existend slide
                if ( this.blurElysiumSlide.name === undefined || this.blurElysiumSlide.name === "" ) {
                    this.createNotificationError({
                        message: this.$tc('BlurElysiumSlides.messages.missingSlideNameError')
                    });                    
                }

                return this.elysiumSlidesRepository.save( this.blurElysiumSlide, Shopware.Context.api ).then(() => {
                    this.isSaveSuccessful = true;
                    this.createNotificationSuccess({
                        message: this.$tc('BlurElysiumSlides.messages.saveSlideSuccess')
                    });
                    this.createdComponent();
                    this.isLoading = false;
                }).catch(( exception ) => {
                    this.createNotificationError({
                        message: this.$tc('BlurElysiumSlides.messages.createSlideError'),
                    });
                    this.isLoading = false;
                });
            } else {

                if ( this.elysiumSlideCreate.name === undefined || this.elysiumSlideCreate.name === "" ) {
                    this.createNotificationError({
                        message: this.$tc('BlurElysiumSlides.messages.missingSlideNameError'),
                    });
                }

                // create new slide
                return this.elysiumSlidesRepository.save( this.elysiumSlideCreate, Shopware.Context.api ).then(() => {
                    
                    this.isSaveSuccessful = true;
                    this.createNotificationSuccess({
                        message: this.$tc('BlurElysiumSlides.messages.createSlideSuccess')
                    });
                    this.createdComponent();
                    this.isLoading = false;
                    this.$router.push({ name: 'blur.elysium.slides.index' });
                }).catch(( exception ) => {
                    this.createNotificationError({
                        message: this.$tc('BlurElysiumSlides.messages.createSlideError')
                    });
                    this.isLoading = false;
                });
            }
        },

        saveOnLanguageChange() {
            return this.onSave();
        },

        abortOnLanguageChange() {
            return this.elysiumSlidesRepository.hasChanges(this.blurElysiumSlide);
        },

        onChangeLanguage( languageId ) {
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

        onDropMedia( mediaItem ) {
            this.setMediaItem( mediaItem.id );
        },

        onUploadMedia( mediaItem ) {
            this.setMediaItem( mediaItem.targetId );
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