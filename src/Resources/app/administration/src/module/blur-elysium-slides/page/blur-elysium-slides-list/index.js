import template from './blur-elysium-slides-list.twig';
import './blur-elysium-slides-list.scss';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register( 'blur-elysium-slides-list', {
    template,

    inject: [
        'repositoryFactory', 
        'acl',
        'filterFactory'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('salutation'),
        Mixin.getByName('listing'),
    ],

    data() {
        return {
            slides: null,
            total: 0,
            isLoading: false,
            term: this.$route.query ? this.$route.query.term : '',
            sortBy: 'name',
            naturalSorting: true,
            sortDirection: 'ASC',
            showDeleteModal: false,
            filterCriteria: []
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {

        repository() {
            return this.repositoryFactory.create( 'blur_elysium_slides' );
        },

        elysiumSlidesColumns() {
            return this.getElysiumSlidesColumns();
        },

        defaultCriteria() {
            const defaultCriteria = new Criteria( this.page, this.limit );

            this.naturalSorting = this.sortBy === 'name';

            defaultCriteria.setTerm(this.term);

            this.sortBy.split(',').forEach(sortBy => {

                defaultCriteria.addSorting( Criteria.sort(
                    sortBy, 
                    this.sortDirection, 
                    this.naturalSorting
                ));
            });

            this.filterCriteria.forEach(filter => {
                defaultCriteria.addFilter(filter);
            });

            return defaultCriteria;
        },
    },

    watch: {
        defaultCriteria: {
            handler() {
                this.getList();
            },
            deep: true
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.getList();
        },

        async getList() {
            this.isLoading = true;

            try {
                const items = await this.repository.search( this.defaultCriteria, Shopware.Context.api );

                this.total = items.total;
                this.slides = items;
                this.isLoading = false;
            } catch {
                this.isLoading = false;
            }
        },

        onDelete( id ) {
            this.showDeleteModal = id;
        },

        onDeleteFinish() {
            this.getList();
        },

        onSearch( searchTerm ) {
            this.term = searchTerm;
        },

        onChangeLanguage(languageId) {
            Shopware.State.commit('context/setApiLanguageId', languageId);
            this.getList();
        },

        onInlineEditSave(promise) {
            promise.then(() => {
                this.createNotificationSuccess({
                    message: this.$tc('sw-customer.detail.messageSaveSuccess'),
                });
            }).catch(() => {
                this.getList();
                this.createNotificationError({
                    message: this.$tc('sw-customer.detail.messageSaveError'),
                });
            });
        },

        updateCriteria(criteria) {
            this.page = 1;
            this.filterCriteria = criteria;
        },

        onDeleteItems() {
            this.getList();
        },

        onCopySlide(referenceSlide) {
            this.isLoading = true
            const cloneOptions = {
                overwrites: {
                    name: `${referenceSlide.name}-${this.$tc('blurElysiumSlider.general.copySuffix')}`,
                },
            };
            this.repository.clone(referenceSlide.id, Shopware.Context.api, cloneOptions).then((result) => {
                this.getList();
            }).catch((error) => {
                console.warn(error)
                this.isLoading = false
            });
        },

        getElysiumSlidesColumns() {
            const columns = [{
                property: 'name',
                dataIndex: 'name',
                inlineEdit: 'string',
                label: 'BlurElysiumSlides.list.columns.name',
                routerLink: 'blur.elysium.slides.detail',
                width: '250px',
                allowResize: true,
                primary: true,
                useCustomSort: true,
                naturalSorting: true,
            }, {
                property: 'title',
                dataIndex: 'title',
                inlineEdit: 'string',
                label: 'BlurElysiumSlides.list.columns.title',
                routerLink: 'blur.elysium.slides.detail',
                width: '1fr',
                allowResize: true,
                sortable: false,
                primary: false,
                useCustomSort: false,
                naturalSorting: false,
            }];

            return columns;
        }
    }
});