import { defaultSlideSettings, defaultContentSettings } from '@elysium/component/slide/settings';
import template from './template.html.twig';
import { ElysiumSlide, SlideError } from '@elysium/types/slide';

const { Component, Context, Mixin, Data, Utils, Store , Classes } = Shopware;
const { Criteria } = Data;
const { ShopwareError } = Classes;

export default Component.wrapComponentConfig({
    template,

    inject: ['repositoryFactory', 'acl'],

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('notification'),
        Mixin.getByName('blur-style-utilities'),
    ],

    props: {
        newSlide: {
            type: Boolean,
            required: true,
            default: false,
        },
        slideId: {
            type: String,
            required: false,
            default: null,
        },
    },

    watch: {
        newSlide(value) {
            if (value === true) {
                this.createSlide();
            }
        },

        slideId() {
            this.loadSlide();
        },

        slide: {
            handler: function (newValue) {
                this.hasChanges = this.slidesRepository.hasChanges(newValue);
            },
            deep: true,
        },
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.metaTitle),
        };
    },

    data() {
        return {
            defaultSlideSettings: structuredClone(defaultSlideSettings),
            defaultContentSettings: structuredClone(defaultContentSettings),
            showDeleteModal: false,
            isLoading: true,
            isSaved: false,
            hasChanges: false,
        };
    },

    computed: {
        context() {
            return Store.get('context');
        },

        elysiumUI() {
            return Store.get('elysiumUI');
        },

        elysiumSlide() {
            return Store.get('elysiumSlide');
        },

        device() {
            return Store.get('elysiumUI').device;
        },

        slide() {
            return this.elysiumSlide.slide;
        },

        error() {
            return Store.get('error');
        },

        contentRoute() {
            if (this.newSlide === false) {
                return {
                    name: 'blur.elysium.slides.detail.content',
                    params: { id: this.slideId },
                };
            }

            return { name: 'blur.elysium.slides.create.content' };
        },

        mediaRoute() {
            if (this.newSlide === false) {
                return {
                    name: 'blur.elysium.slides.detail.media',
                    params: { id: this.slideId },
                };
            }

            return { name: 'blur.elysium.slides.create.media' };
        },

        displayRoute() {
            if (this.newSlide === false) {
                return {
                    name: 'blur.elysium.slides.detail.display',
                    params: { id: this.slideId },
                };
            }

            return { name: 'blur.elysium.slides.create.display' };
        },

        advancedRoute() {
            if (this.newSlide === false) {
                return {
                    name: 'blur.elysium.slides.detail.advanced',
                    params: { id: this.slideId },
                };
            }

            return { name: 'blur.elysium.slides.create.advanced' };
        },

        slidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        },

        customFieldSetRepository() {
            return this.repositoryFactory.create('custom_field_set');
        },

        cancelActionMessage(): string {
            if (this.newSlide === true) {
                return this.$tc(
                    'blurElysiumSlides.messages.cancelSlideCreation'
                );
            }

            return this.$tc('blurElysiumSlides.messages.cancelSlideChanges');
        },

        metaTitle() {
            return this.placeholder(
                this.slide,
                'name',
                this.$tc('blurElysiumSlides.actions.newSlide')
            );
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

        tabContentHasError() {
            if (
                this.slide.slideSettings.slide.linking.type === 'product' &&
                (this.slide.productId === undefined ||
                    this.slide.productId === null ||
                    this.slide.productId === '')
            ) {
                return true;
            }

            return false;
        },

        tabAdvancedHasWarning() {
            if (this.slide.slideSettings.customTemplateFile) {
                return true;
            }
            return false;
        },

        tabAdvancedWarningMessage() {
            return this.$t(
                'blurElysiumSlides.messages.customTemplateFileDefinedNotice'
            );
        },
    },

    methods: {
        setMediaSidebar(element) {
            this.elysiumUI.setMediaSidebar(element);
        },

        createSlide() {
            this.context.resetLanguageToDefault();
            const slide = this.slidesRepository.create(Context.api);
            Object.assign(slide, { 
                slideSettings: this.defaultSlideSettings,
                contentSettings: this.defaultContentSettings
            });
            this.elysiumSlide.setSlide(slide);
            this.isLoading = false;
        },

        deleteSlide() {
            this.isLoading = true;

            this.slidesRepository
                .delete(this.slideId, Context.api)
                .then(() => {
                    this.$emit('delete-finish');
                    this.$router.push({ name: 'blur.elysium.slides.overview' });
                })
                .catch((error) => {
                    console.error(error);
                });
        },

        loadSlide() {

            const criteria = new Criteria();
            criteria.addAssociation('product');
            criteria.addAssociation('product.cover');
            criteria.addAssociation('product.cover.media');

            this.slidesRepository
                .get(this.slideId, Context.api, criteria)
                .then((slide) => {
                    const mergedSlide = this._mergeSettings(slide, {
                        slideSettings: this.defaultSlideSettings,
                        contentSettings: this.defaultContentSettings,
                    })
                    this.elysiumSlide.setSlide(mergedSlide);
                    this.loadCustomFieldSets();
                })
                .catch((exception) => {
                    console.warn(exception);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        loadCustomFieldSets() {
            const criteria = new Criteria();

            criteria.addFilter(
                Criteria.equals('relations.entityName', 'blur_elysium_slides')
            );

            criteria
                .getAssociation('customFields')
                .addSorting(Criteria.sort('config.customFieldPosition'));

            this.customFieldSetRepository
                .search(criteria, Context.api)
                .then((result) => {
                    this.elysiumSlide.setCustomFieldSet(result);
                })
                .catch((exception) => {
                    console.warn(exception);
                });
        },

        overviewPush() {
            this.$router.push({ name: 'blur.elysium.slides.overview' });
        },

        detailPush(id: string) {
            this.$router.push({
                name: 'blur.elysium.slides.detail',
                params: { id },
            });
        },

        async saveSlide() {
            if (
                !(
                    (this.newSlide && this.permissionCreate) ||
                    this.permissionEdit
                )
            ) {
                return;
            }

            this.isLoading = true;

            if (
                this.slide.slideSettings.slide.linking.type === 'product' &&
                [undefined, null, ''].includes(this.slide.productId)
            ) {
                this.createNotificationError({
                    message: this.$t(
                        'blurElysiumSlides.messages.productLinkingMissingEntity'
                    ),
                });
                this.isLoading = false;
                return;
            }

            this.slidesRepository
                .save(this.slide)
                .then((result) => {
                    this.createNotificationSuccess({
                        message: this.$t(
                            'blurElysiumSlides.messages.slideSavedSuccess',
                            { slide: this.slide.name }
                        ),
                    });

                    if (this.newSlide === true) {
                        // push to detail route
                        this.detailPush(JSON.parse(result.config.data).id);
                    }

                    this.loadSlide();
                })
                .catch((reason) => {
                    const errors: SlideError[] | null = reason.response?.data?.errors || null

                    if (!this.hasSlideName(this.slide?.name)) {
                        this.addNotificationError(
                            this.$tc('blurElysiumSlides.messages.emptySlideNameErrorTitle'),
                            this.$tc('blurElysiumSlides.messages.emptySlideNameError')
                        )
                    } else if (errors !== null && errors.length > 0) {
                        errors.forEach((error) => {
                            this.sendError(error)
                        })
                    } else {
                        this.createNotificationError({
                            title: this.$tc('blurElysiumSlides.messages.slideSaveErrorTitle'),
                            message: this.$tc('blurElysiumSlides.messages.slideSaveError'),
                        });
                    }


                    console.error(errors)
                    this.isLoading = false
                });
        },

        hasSlideName(name: String | undefined | null): boolean {
            return Boolean(name)
        },

        sendError(error: SlideError, sendNotification = true) {
            this.sendApiError(error)
            if (sendNotification) {
                this.sendNotificationError(
                    this.$t(`blurElysiumSlides.violations.${error.code}.title`),
                    this.$t(`blurElysiumSlides.violations.${error.code}.message`)
                )
            }
        },

        sendNotificationError(title: string, message: string) {
            this.createNotificationError({
                title,
                message,
            });
        },

        sendApiError(error: SlideError) {
            const property = error.source.pointer.replace(/^\/|\/+$/g, '').replace(/\/+/g, '.')
            this.error.addApiError({
                expression: `blur_elysium_slides.${this.slide.id}.${property}`,
                error: new ShopwareError({
                    code: error.code,
                    detail: this.$t(`blurElysiumSlides.violations.${error.code}.message`),
                    meta: error.meta,
                })
            });
        },

        cancelAction() {
            if (this.newSlide === true) {
                this.overviewPush();
            } else {
                this.$router.go(0);
            }
        },

        saveOnLanguageChange() {
            this.saveSlide();
        },

        abortOnLanguageChange() {
            return this.slidesRepository.hasChanges(this.slide);
        },

        onChangeLanguage(languageId: string) {
            this.context.setApiLanguageId(languageId);

            if (this.slideId) {
                this.loadSlide();
            }
        },

        onCopySlide() {
            if (this.permissionCreate !== true) {
                return;
            }

            if (this.slidesRepository.hasChanges(this.slide)) {
                this.createNotificationError({
                    message: this.$tc(
                        'blurElysiumSlides.messages.copyErrorUnsavedChanges'
                    ),
                });
                return;
            }

            const cloneOptions = {
                overwrites: {
                    name: `${this.slide.name}-${this.$tc('blurElysium.general.copySuffix')}`,
                },
            };

            this.isLoading = true;

            this.slidesRepository
                .clone(this.slide.id, cloneOptions)
                .then((result) => {
                    this.$router.push({
                        name: 'blur.elysium.slides.detail',
                        params: { id: result.id },
                    });
                })
                .catch((error) => {
                    console.warn(error);
                });
        },

        _mergeSettings(slide: ElysiumSlide, properties: Record<string, unknown>) {
            const slideObj = slide as unknown as Record<string, unknown>;
            Object.entries(properties).forEach(([key, defaultSettings]) => {
                if (slideObj[key]) {
                    slideObj[key] = Utils.object.deepMergeObject(defaultSettings as object, slideObj[key] as object);
                } else {
                    slideObj[key] = defaultSettings;
                }
            });
            
            return slide;
        }
    },

    created() {
        if (this.newSlide === true) {
            this.createSlide();
        } else {
            this.loadSlide();
        }
    },

    unmounted() {
        this.elysiumSlide.clearSlide();
        this.elysiumSlide.clearCustomFieldSet();
        this.elysiumUI.resetDevice();
    },
});
