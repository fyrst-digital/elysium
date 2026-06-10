import { module } from '@elysium/meta';
import { getDisplayContentSettings } from '@elysium/composables/content-settings-display';
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
    showImportModal: boolean;
    importFile: File | null;
    isImporting: boolean;
    selection: Record<string, unknown>;
}

export default Component.wrapComponentConfig({
    template,

    inject: ['repositoryFactory', 'acl', 'feature'],

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
                    property: 'contentSettings.title',
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
            showImportModal: false,
            importFile: null,
            isImporting: false,
            selection: {},
        };
    },

    setup() {
        return {
            module,
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
            criteria.addAssociation('translations');

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

        permissionExport() {
            return this.acl.can('blur_elysium_slides.exporter');
        },

        permissionImport() {
            return this.acl.can('blur_elysium_slides.importer');
        },

        isImportExportEnabled() {
            return this.feature.isActive('elysium_preview_import_export');
        },

        assetFilter() {
            return Filter.getByName('asset');
        },
    },

    methods: {
        getList() {
            this.isLoading = true;

            this.slidesRepository
                .search(this.defaultCriteria, { ...Context.api, inheritance: true })
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

        displayContentSettings(item) {
            return getDisplayContentSettings(item);
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

        getSlideHints(slide: { customFields: Record<string, unknown> | null; activeFrom: string | null; activeUntil: string | null }): string[] {
            const hints: string[] = [];

            if (slide.customFields && Object.keys(slide.customFields).length > 0) {
                hints.push(this.$tc('blurElysiumSlides.grid.hints.hasCustomFields'));
            }

            if (slide.activeFrom) {
                hints.push(this.$tc('blurElysiumSlides.grid.hints.hasActiveFrom'));
            }

            if (slide.activeUntil) {
                hints.push(this.$tc('blurElysiumSlides.grid.hints.hasActiveUntil'));
            }

            return hints;
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

        onSelectionChange(selection) {
            this.selection = selection;
        },

        downloadBlob(blob: Blob, filename: string): void {
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        },

        generateExportFilename(): string {
            const timestamp = new Date().toISOString().slice(0, 19).replace(/[-:T]/g, '-');
            return `elysium-slides-export-${timestamp}.jsonl`;
        },

        performExport(ids: string[]): Promise<void> {
            const token = Shopware.Service('loginService').getToken();

            return fetch('/api/_action/elysium-slides/export', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify({ ids }),
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Export failed');
                    }
                    const filename = this.generateExportFilename();
                    return response.blob().then((blob) => ({ blob, filename }));
                })
                .then(({ blob, filename }) => {
                    this.downloadBlob(blob, filename);
                    this.createNotificationSuccess({
                        message: this.$t('blurElysiumSlides.messages.exportSuccess', { filename }),
                    });
                });
        },

        onBulkExport() {
            if (!this.permissionExport || !this.isImportExportEnabled) {
                return;
            }

            const ids = Object.values(this.selection).map((item: { id: string }) => item.id);

            if (ids.length === 0) {
                return;
            }

            this.performExport(ids)
                .catch((error) => {
                    console.error(error);
                    this.createNotificationError({
                        message: this.$t('blurElysiumSlides.messages.exportError'),
                    });
                });
        },

        onExportAll() {
            if (!this.permissionExport || !this.isImportExportEnabled) {
                return;
            }

            this.performExport([])
                .catch((error) => {
                    console.error(error);
                    this.createNotificationError({
                        message: this.$t('blurElysiumSlides.messages.exportError'),
                    });
                });
        },

        onImportSlides() {
            if (!this.permissionImport || !this.isImportExportEnabled) {
                return;
            }

            this.showImportModal = true;
            this.importFile = null;
        },

        onCloseImportModal() {
            this.showImportModal = false;
            this.importFile = null;
        },

        onImportFileChange(file: File) {
            this.importFile = file;
        },

        onSubmitImport() {
            if (!this.importFile) {
                return;
            }

            this.isImporting = true;

            const formData = new FormData();
            formData.append('file', this.importFile);

            const token = Shopware.Service('loginService').getToken();

            fetch('/api/_action/elysium-slides/import', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                body: formData,
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Import failed');
                    }
                    return response.json();
                })
                .then((data) => {
                    this.isImporting = false;
                    this.showImportModal = false;

                    if (data.success) {
                        this.createNotificationSuccess({
                            message: this.$tc('blurElysiumSlides.messages.importSuccess', data.imported, { count: data.imported }),
                        });
                        this.getList();
                    } else {
                        const errors = (data.errors && data.errors.length > 0) ? data.errors.join(', ') : this.$t('blurElysiumSlides.messages.importError');
                        this.createNotificationError({
                            message: this.$t('blurElysiumSlides.messages.importError', { message: errors }),
                        });
                    }
                })
                .catch((error) => {
                    this.isImporting = false;
                    console.error(error);
                    this.createNotificationError({
                        message: this.$t('blurElysiumSlides.messages.importError', { message: error.message }),
                    });
                });
        },
    },

    created() {
        this.getList();
    },
});
