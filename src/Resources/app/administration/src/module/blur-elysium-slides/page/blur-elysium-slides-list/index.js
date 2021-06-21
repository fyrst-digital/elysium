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

    data() {
        return {
            blur_elysium_slides: null,
            total: 0,
            blur_elysium_slide: null,
            isLoading: false,
            term: this.$route.query ? this.$route.query.term : '',
            filterCriteria: []
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

        defaultCriteria() {
            const defaultCriteria = new Criteria( this.page, this.limit );

            defaultCriteria.setTerm(this.term);

            this.filterCriteria.forEach(filter => {
                defaultCriteria.addFilter(filter);
            });

            /*
            const defaultCriteria = new Criteria(this.page, this.limit);
            // eslint-disable-next-line vue/no-side-effects-in-computed-properties
            this.naturalSorting = this.sortBy === 'customerNumber';

            defaultCriteria.setTerm(this.term);

            this.sortBy.split(',').forEach(sortBy => {
                defaultCriteria.addSorting(Criteria.sort(sortBy, this.sortDirection, this.naturalSorting));
            });

            defaultCriteria
                .addAssociation('defaultBillingAddress')
                .addAssociation('group')
                .addAssociation('requestedGroup')
                .addAssociation('salesChannel');

            this.filterCriteria.forEach(filter => {
                defaultCriteria.addFilter(filter);
            });
            */

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
            console.dir( this.blur_elysium_slides );
        },

        async getList() {
            this.isLoading = true;

            /**
                const criteria = await Shopware.Service('filterService')
                    .mergeWithStoredFilters(this.storeKey, this.defaultCriteria);

                this.activeFilterNumber = criteria.filters.length;
             */


            try {
                const items = await this.elysiumSlidesRepository.search( this.defaultCriteria, Shopware.Context.api );

                this.total = items.total;
                this.blur_elysium_slides = items;
                this.isLoading = false;
                this.selection = {};

                console.dir( this.blur_elysium_slides );
            } catch {
                this.isLoading = false;
            }
        },

        onSearch( searchTerm ) {
            this.term = searchTerm;
            // this.clearSelection();
        },

        onChangeLanguage(languageId) {
            Shopware.State.commit('context/setApiLanguageId', languageId);
            this.getList();
        },

        updateCriteria(criteria) {
            this.page = 1;
            this.filterCriteria = criteria;
        },
    }
});