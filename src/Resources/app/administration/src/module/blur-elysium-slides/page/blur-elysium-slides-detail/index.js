import template from './blur-elysium-slides-detail.twig';
import './blur-elysium-slides-detail.scss';
import slideStates from './state';

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
            media: {
                slideCover: {
                    data: null,
                    slideMediaId: 'mediaId'
                },
                slideCoverPortrait: {
                    data: null,
                    slideMediaId: 'mediaPortraitId'
                }
            },
            customFieldSets: [],
            detailRoute: 'blur.elysium.slides.detail',
            createRoute: 'blur.elysium.slides.create'
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

        elysiumSlidesSyncRepository() {
            return this.repositoryFactory.create('blur_elysium_slides', null, { useSync: true });
        },

        customFieldSetRepository() {
            return this.repositoryFactory.create( 'custom_field_set' );
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        defaultCriteria() {
            const criteria = new Criteria();

            return criteria;
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
        }
    },

    beforeCreate() {
        Shopware.State.registerModule( 'blurElysiumSlidesDetail', slideStates);
    },

    created() {
        this.createdComponent()
        this.loadCustomFieldSets()
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
                this.loadSlide()           
            } else {
                /**
                 * if slide id is not provided create new slide
                **/
                this.createSlide()
            }
        },

        createSlide() {
            let newSlide = this.elysiumSlidesRepository.create( Shopware.Context.api )
            newSlide.slideSettings = {}
            
            this.setSlide( newSlide )
        },

        setSlide( slide ) {
            this.blurElysiumSlide = slide
            Shopware.State.commit('blurElysiumSlidesDetail/setSlide', slide);
        },

        getSlide() {
            return this.blurElysiumSlide
        },

        setSlideProp( key, value ) {
            this.blurElysiumSlide[key] = value
        },

        getSlideProp( key ) {
            return this.blurElysiumSlide[key]
        },

        initState() {
            Shopware.State.commit('blurElysiumSlidesDetail/setApiContext', Shopware.Context.api);

            // when product exists
            if ( this.blurElysiumSlideId ) {
                return this.loadState();
            }
        },

        loadCustomFieldSets() {

            this.customFieldSetRepository.search( this.customFieldSetCriteria, Shopware.Context.api )
            .then( ( result ) => {
                this.customFieldSets = result.filter((set) => set.customFields.length > 0);
                this.isLoading = false;
            }, reason => {
                this.customFieldSets = [];
            }).catch(( exception ) => {
                console.warn( exception )
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
            ).then(( blurElysiumSlide ) => {
                
                this.blurElysiumSlide = blurElysiumSlide;
                this.isLoading = false;

                Shopware.State.commit('blurElysiumSlidesDetail/setSlide', blurElysiumSlide);
                Shopware.State.commit('blurElysiumSlidesDetail/setLoading', ['slide', false]);
            }).catch(( exception ) => {
                console.warn( exception )
            })
        },

        saveFinish() {
            this.isSaveSuccessful = false;
            // this.editMode = true;
        },

        async onSave() {
            this.isLoading = true;
            this.isSaveSuccessful = false;

            if ( !( this.blurElysiumSlide === null || this.blurElysiumSlide === undefined ) ) {

                // throw error notification if slide name is missing or empty
                if ( this.blurElysiumSlide.name === undefined || this.blurElysiumSlide.name === "" ) {
                    this.createNotificationError({
                        message: this.$tc('BlurElysiumSlides.messages.missingSlideNameError')
                    });                    
                }

                // save slide
                return this.elysiumSlidesRepository.save( this.blurElysiumSlide ).then((result) => {
                    this.isSaveSuccessful = true;
                    this.createNotificationSuccess({
                        message: this.$tc('BlurElysiumSlides.messages.saveSlideSuccess')
                    });

                    // push route to detail with new id as param
                    this.$router.push({ 
                        name: 'blur.elysium.slides.detail',
                        params: {
                            id: JSON.parse( result.config.data ).id
                        } 
                    })

                    this.isLoading = false;
                }).catch(( exception ) => {
                    this.createNotificationError({
                        message: this.$tc('BlurElysiumSlides.messages.createSlideError'),
                    });
                    this.isLoading = false;
                });
            } else {

                console.error('Slide Entity is missing')

                // throw error notification if slide name is missing or empty
                this.createNotificationError({
                    message: this.$tc('BlurElysiumSlides.messages.missingSlideEntity'),
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

        cancel() {
            this.$router.push({ name: 'blur.elysium.slides.index' });
        },

        setMedia( id, key ) {

            this.mediaRepository.get( id, Shopware.Context.api )
            .then( ( media ) => {
                this.blurElysiumSlide[ this.media[key].slideMediaId ] = id
                this.media[key].data = media
                
            }).catch(( exception ) => {
                console.warn( exception );
            });
        },
    }
});