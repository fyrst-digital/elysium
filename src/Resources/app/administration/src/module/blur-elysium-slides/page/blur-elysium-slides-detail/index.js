import template from './blur-elysium-slides-detail.twig';
import './blur-elysium-slides-detail.scss';
import slideStates from './state';
import { slides } from "@elysiumSlider/utilities/identifiers";

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;
const { mapMutations, mapState } = Component.getComponentHelper();

/**
 * @todo improve code quality in `blur-elysium-slides-detail` component
 * - replace state commits with mutations
 * - replace data based loading state mutation with reactive state mutation (e.g. `this.isLoading = false` becomes `this.setLoading({...})`)
 * - make `èditMode` stateful and reactive
 * - make `ACL` stateful and reactive
 */

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
            isSaveSuccessful: false,
            isNewSlide: null,
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
            'slide',
            'customFieldSets',
            'loading',
            'acl'
        ]),

        contentRoute() {
            if (this.blurElysiumSlideId) {
                return { name: 'blur.elysium.slides.detail.content', params: { id: this.blurElysiumSlideId } }
            }

            return { name: 'blur.elysium.slides.create.content' }
        },

        mediaRoute() {
            if (this.blurElysiumSlideId) {
                return { name: 'blur.elysium.slides.detail.media', params: { id: this.blurElysiumSlideId } }
            }

            return { name: 'blur.elysium.slides.create.media' }
        },
        
        displayRoute() {
            if (this.blurElysiumSlideId) {
                return { name: 'blur.elysium.slides.detail.display', params: { id: this.blurElysiumSlideId } }
            }

            return { name: 'blur.elysium.slides.create.display' }
        },

        advancedRoute() {
            if (this.blurElysiumSlideId) {
                return { name: 'blur.elysium.slides.detail.advanced', params: { id: this.blurElysiumSlideId } }
            }

            return { name: 'blur.elysium.slides.create.advanced' }
        },

        identifier() {
            return this.slideLabel;
        },

        positionIdentifiers() {
            return slides
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
        Shopware.State.registerModule('blurElysiumSlidesDetail', slideStates);
        console.log(this);
    },

    beforeDestroy() {
        Shopware.State.unregisterModule('blurElysiumSlidesDetail');
        console.log('destroy')
    },

    created() {
        this.setAclStates([
            'viewer',
            'editor',
            'creator',
            'deleter',
        ])
        this.createdComponent()
        this.loadCustomFieldSets()
    },

    methods: {
        ...mapMutations('blurElysiumSlidesDetail', [
            'setApiContext',
            'setSlide',
            'setCustomFieldSets',
            'setAcl',
            'setMediaSidebar',
            'setLoading'
        ]),

        setAclStates( roles ) {
            roles.forEach(role => {
                this.setAcl({
                    role: role, 
                    state: this.acl.can(`blur_elysium_slides.${role}`)
                })
            });
        },

        createdComponent() {
            if (this.blurElysiumSlideId === null) {  
                console.log('create comp')
                this.createSlide()
            } else {
                this.setLoading('slide', true)
                this.loadSlide() 
            }
        },

        createSlide() {
            Shopware.State.commit('context/resetLanguageToDefault');
            let newSlide = this.elysiumSlidesRepository.create( Shopware.Context.api )
            newSlide.slideSettings = {}

            this.setSlide( newSlide )
            console.log('createSlide', this.slide)
        },

        loadCustomFieldSets() {

            this.customFieldSetRepository.search( this.customFieldSetCriteria, Shopware.Context.api )
            .then( ( result ) => {
                this.setCustomFieldSets( result )
            }).catch(( exception ) => {
                console.warn( exception )
            });
        },

        loadSlide() {
            this.setLoading({
                key: 'slide',
                loading: true
            })

            this.elysiumSlidesRepository.get(
                this.blurElysiumSlideId,
                Shopware.Context.api,
                this.defaultCriteria,
            ).then(( slide ) => {
                this.setSlide(slide)
                this.setLoading({
                    key: 'slide',
                    loading: false
                })
            }).catch(( exception ) => {
                console.warn( exception )
            })
        },

        saveFinish() {
            this.isSaveSuccessful = false
        },

        detailPush( id ) {
            this.$router.push({ name: 'blur.elysium.slides.detail', params: { id: id } });
        },

        async onSave() {
            /**
             * @todo 
             * - rewrite save method and improve code quality
             * - get rid of `this.blurElysiumSlide`
             */
            
            this.setLoading({
                key: 'slide',
                loading: true
            })

            this.isSaveSuccessful = false;

            if ( !( this.slide === null || this.slide === undefined ) ) {

                // throw error notification if slide name is missing or empty
                if ( this.slide.name === undefined || this.slide.name === "" ) {
                    this.createNotificationError({
                        message: this.$tc('BlurElysiumSlides.messages.missingSlideNameError')
                    });                    
                }

                // save slide
                return this.elysiumSlidesRepository.save( this.slide ).then((result) => {
                    this.createNotificationSuccess({
                        message: this.$tc('BlurElysiumSlides.messages.saveSlideSuccess')
                    });

                    if (this.slide._isNew === true) {
                        this.detailPush(JSON.parse( result.config.data ).id)
                    } else {
                        this.isSaveSuccessful = true;
                        this.loadSlide()
                    }
                }).catch(( exception ) => {
                    this.createNotificationError({
                        message: this.$tc('BlurElysiumSlides.messages.createSlideError'),
                    });
                    console.warn( exception )
                    this.setLoading({
                        key: 'slide',
                        loading: false
                    })
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
            return this.elysiumSlidesRepository.hasChanges(this.slide);
        },

        onChangeLanguage( languageId ) {
            Shopware.State.commit('context/setApiLanguageId', languageId);
            
            this.setApiContext(Shopware.Context.api)

            // when product exists
            if ( this.blurElysiumSlideId ) {
                return this.loadSlide();
            }
        },

        onActivateCustomerEditMode() {
            this.editMode = true;
        },

        cancel() {
            this.$router.push({ name: 'blur.elysium.slides.index' });
        }
    }
});