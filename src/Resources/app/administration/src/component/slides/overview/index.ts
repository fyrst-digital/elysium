import { module } from '@elysium/meta';
import template from './template.html.twig';

const { Component, Mixin, Data, Filter, Context, Store } = Shopware;
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
                    inlineEdit: 'string',
                    label: 'blurElysiumSlides.grid.nameLabel',
                    routerLink: 'blur.elysium.slides.detail',
                    width: '250px',
                    allowResize: true,
                    primary: true,
                },
                {
                    property: 'title',
                    inlineEdit: 'string',
                    label: 'blurElysiumSlides.grid.headlineLabel',
                    routerLink: 'blur.elysium.slides.detail',
                    width: '1fr',
                    allowResize: true,
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

    mixins: [
        Mixin.getByName('notification'), 
        Mixin.getByName('listing')
    ],

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    watch: {
        searchTerm: {
            handler() {
                this.getList();
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
                Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting)
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
        getList() {
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
            Store.get('context').setApiLanguageId(languageId);
            this.isLoading = true;
            this.getList();
        },

        onColumnSort(column) {
            this.onSortColumn(column);
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
                    this.getList();
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
            this.getList();
        },
    },

    created() {
        this.getList();
    },
});
