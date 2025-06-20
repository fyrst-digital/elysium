import { module } from '@elysium/meta';
import template from './template.html.twig';
import EntityCollection from 'shopware/core/data/entity-collection.data';

const { Component, Mixin, Data, State, Filter, Context, Store } = Shopware;
const { Criteria } = Data;

type SortDirection = 'ASC' | 'DESC';

interface Data {
    slidesCollection: EntityCollection<'blur_elysium_slides'>;
    slidesColumns: object[];
    isLoading: boolean;
    searchTerm: string;
    sortBy: string;
    sortDirection: SortDirection;
    total: number | null;
    styles: object;
}

export default Component.wrapComponentConfig({
    template,

    inject: ['repositoryFactory', 'acl'],

    setup() {
        return {
            module,
        };
    },

    data() {
        return <Data>{
            slidesCollection: {},
            slidesColumns: [
                {
                    property: 'name',
                    dataIndex: 'name',
                    inlineEdit: 'string',
                    label: 'blurElysiumSlides.grid.nameLabel',
                    routerLink: 'blur.elysium.slides.detail',
                    width: '250px',
                    allowResize: true,
                    primary: true,
                    useCustomSort: true,
                    naturalSorting: true,
                },
                {
                    property: 'title',
                    dataIndex: 'title',
                    inlineEdit: 'string',
                    label: 'blurElysiumSlides.grid.headlineLabel',
                    routerLink: 'blur.elysium.slides.detail',
                    width: '1fr',
                    allowResize: true,
                    sortable: false,
                    primary: false,
                    useCustomSort: false,
                    naturalSorting: false,
                },
            ],
            isLoading: true,
            searchTerm: this.$route.query?.term ? this.$route.query.term : '',
            sortBy: 'createdAt',
            sortDirection: 'DESC',
            styles: {
                moduleHeading: <CSSStyleDeclaration>{
                    display: 'flex',
                    gap: '16px',
                    alignItems: 'center',
                },
            },
        };
    },

    mixins: [Mixin.getByName('notification'), Mixin.getByName('listing')],

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    watch: {
        searchTerm: {
            handler() {
                this.loadSlides();
            },
        },
    },

    computed: {
        slidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        },

        defaultCriteria() {
            const criteria = new Criteria(this.page, this.limit);

            criteria.setTerm(this.searchTerm);

            criteria.addSorting(
                Criteria.sort(this.sortBy, this.sortDirection, true)
            );

            return criteria;
        },

        permissionView() {
            return this.acl.can('blur_elysium_slides.viewer');
        },

        permissionCreate() {
            return this.acl.can('blur_elysium_slides.creator');
        },

        permissionEdit() {
            return this.acl.can('blur_elysium_slides.editor');
        },

        permissionDelete() {
            return this.acl.can('blur_elysium_slides.deleter');
        },

        assetFilter() {
            return Filter.getByName('asset');
        },
    },

    methods: {
        loadSlides() {
            this.isLoading = true;

            this.slidesRepository
                .search(this.defaultCriteria, Context.api)
                .then((result) => {
                    this.slidesCollection = result;
                    this.total =
                        typeof result.total === 'number' ? result.total : 0;
                    this.isLoading = false;
                })
                .catch((error) => {
                    console.error(error);
                    this.isLoading = false;
                });
        },

        onSearch(searchTerm: string) {
            this.searchTerm = searchTerm;
        },

        onChangeLanguage(languageId: string) {
            State.commit('context/setApiLanguageId', languageId);
            this.isLoading = true;
            this.loadSlides();
        },

        columnSort() {
            this.loadSlides();
        },

        copySlide(slide) {
            if (this.permissionCreate !== true) {
                return;
            }

            this.isLoading = true;

            const cloneOptions = {
                overwrites: {
                    name: `${slide.name}-${this.$tc('blurElysium.general.copySuffix')}`,
                },
            };

            this.slidesRepository
                .clone(slide.id, cloneOptions)
                .then(() => {
                    this.loadSlides();
                })
                .catch((error) => {
                    console.warn(error);
                    this.isLoading = false;
                });
        },

        finishDeleteItems() {
            this.createNotificationSuccess({
                message: this.$tc(
                    'blurElysiumSlides.messages.slideDeleteSucces'
                ),
            });
            this.isLoading = false;
            this.loadSlides();
        },
    },

    created() {
        this.loadSlides();
    },
});
