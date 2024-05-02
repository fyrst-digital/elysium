
import defaultSlideSettings from 'blurElysium/component/slides/settings'
import template from './template.html.twig'

const { Component, State, Context, Mixin, Data, Utils } = Shopware
const { Criteria } = Data
const { mapMutations, mapState } = Component.getComponentHelper()

export default Component.wrapComponentConfig({
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('notification'),
    ],

    props: {
        newSlide: {
            type: Boolean,
            required: true,
            default: false
        },
        slideId: {
            type: String,
            required: false,
            default: null
        }
    },

    watch: {
        newSlide(value) {
            if (value === true) {
                this.createSlide()
            }
        },

        slideId() {
            this.loadSlide()
        },

        slide: {
            handler: function (newValue) {
                this.hasChanges = this.slidesRepository.hasChanges(newValue)
            },
            deep: true
        }
    },

    metaInfo () {
        return {
            title: this.$createTitle(this.metaTitle)
        }
    },

    data () {
        return {
            defaultSlideSettings: structuredClone(defaultSlideSettings),
            showDeleteModal: false,
            isLoading: true,
            isSaved: false,
            hasChanges: false
        }
    },

    computed: {

        ...mapState('blurElysiumSlide', [
            'slide',
            'deviceView'
        ]),

        contentRoute (): any {
            if (this.newSlide === false) {
                return { name: 'blur.elysium.slides.detail.content', params: { id: this.slideId } }
            }

            return { name: 'blur.elysium.slides.create.content' }
        },

        mediaRoute (): any {
            if (this.newSlide === false) {
                return { name: 'blur.elysium.slides.detail.media', params: { id: this.slideId } }
            }

            return { name: 'blur.elysium.slides.create.media' }
        },

        displayRoute (): any {
            if (this.newSlide === false) {
                return { name: 'blur.elysium.slides.detail.display', params: { id: this.slideId } }
            }

            return { name: 'blur.elysium.slides.create.display' }
        },

        advancedRoute (): any {
            if (this.newSlide === false) {
                return { name: 'blur.elysium.slides.detail.advanced', params: { id: this.slideId } }
            }

            return { name: 'blur.elysium.slides.create.advanced' }
        },

        slidesRepository () {
            return this.repositoryFactory.create('blur_elysium_slides')
        },

        customFieldSetRepository () {
            return this.repositoryFactory.create('custom_field_set')
        },

        customFieldSetCriteria () {
            const criteria = new Criteria()

            criteria.addFilter(
                Criteria.equals('relations.entityName', 'blur_elysium_slides')
            )

            criteria.getAssociation('customFields')
                    .addSorting(Criteria.sort('config.customFieldPosition'))

            return criteria
        },

        cancelActionMessage (): string {
            if (this.newSlide === true) {
                return this.$tc('blurElysiumSlides.messages.cancelSlideCreation')
            }

            return this.$tc('blurElysiumSlides.messages.cancelSlideChanges')
        },

        metaTitle () {
            return this.placeholder(this.slide, <any>'name', this.$tc('blurElysiumSlides.actions.newSlide'))
        }
    },

    methods: {

        ...mapMutations('blurElysiumSlide', [
            'setSlide',
            'setSlideProperty',
            'setCustomFieldSet',
            'setMediaSidebar',
            'setDeviceView'
        ]),

        createSlide () {
            State.commit('context/resetLanguageToDefault')
            const slide = this.slidesRepository.create(Context.api)
            Object.assign(slide, { slideSettings: this.defaultSlideSettings })
            this.setSlide(slide)
            this.isLoading = false
        },

        deleteSlide () {
            this.isLoading = true

            this.slidesRepository.delete(this.slideId, Context.api).then(() => {
                this.$emit('delete-finish')
                this.$router.push({ name: 'blur.elysium.slides.overview' })
            }).catch((error) => {
                console.error(error)
            })
        },

        loadSlide () {

            this.slidesRepository.get(
                this.slideId,
                Context.api,
                new Criteria
            ).then((slide: any) => {
                const mergedSlideSettings = Utils.object.deepMergeObject(this.defaultSlideSettings, slide.slideSettings)
                slide.slideSettings = mergedSlideSettings
                this.setSlide(slide)
            }).catch((exception) => {
                console.warn(exception)
            })

            this.isLoading = false
        },

        loadCustomFieldSets () {
            this.customFieldSetRepository.search(this.customFieldSetCriteria, Context.api)
            .then((result) => {
                this.setCustomFieldSet(result)
            }).catch((exception) => {
                console.warn(exception)
            })
        },

        viewportChanged (value: string) {
            this.setDeviceView(value)
        },

        overviewPush () {
            this.$router.push({ name: 'blur.elysium.slides.overview' })
        },

        detailPush (id: string) {
            this.$router.push({ name: 'blur.elysium.slides.detail', params: { id } })
        },

        async saveSlide () {
            this.isLoading = true

            this.slidesRepository.save(this.slide)
            .then((result: any) => {

                this.createNotificationSuccess({
                    message: this.$t('blurElysiumSlides.messages.slideSavedSuccess', { slide: this.slide.name })
                })

                if (this.newSlide === true) {
                    // push to detail route
                    this.detailPush(JSON.parse(result.config.data).id)
                } else {
                    // just load slide
                    this.loadSlide()
                }

                this.isLoading = false
            }).catch((reason) => {

                if (this.slide.name === undefined || this.slide.name === null || this.slide.name === '') {
                    this.createNotificationError({
                        title: this.$tc('blurElysiumSlides.messages.emptySlideNameErrorTitle'),
                        message: this.$tc('blurElysiumSlides.messages.emptySlideNameError')
                    })
                } else {
                    this.createNotificationError({
                        message: this.$tc('blurElysiumSlides.messages.slideSaveError')
                    })
                }
                console.error(reason)
                this.isLoading = false
            })
        },

        cancelAction () {
            if (this.newSlide === true) {
                this.overviewPush()
            } else {
                this.$router.go(0)
            }
        },

        saveOnLanguageChange () {
            this.saveSlide()
        },

        abortOnLanguageChange () {
            return this.slidesRepository.hasChanges(this.slide)
        },

        onChangeLanguage (languageId: string) {
            State.commit('context/setApiLanguageId', languageId)

            // this.setApiContext(Context.api)

            if (this.slideId) {
                this.loadSlide()
            }
        },

        onCopySlide () {
            if (this.slidesRepository.hasChanges(this.slide)) {
                this.createNotificationError({
                    message: this.$tc('blurElysiumSlides.messages.copyErrorUnsavedChanges')
                })
                return
            }
            const cloneOptions = <any>{
                overwrites: {
                    name: `${this.slide.name}-${this.$tc('blurElysium.general.copySuffix')}`
                }
            }

            this.isLoading = true

            this.slidesRepository.clone(this.slide.id, cloneOptions).then((result: any) => {
                this.$router.push({ name: 'blur.elysium.slides.detail', params: { id: result.id } })
            }).catch((error) => {
                console.warn(error)
            })
        },

        setSlideCoverImage (media: any) {
            const mappedViewportFields = <any>{
                mobile: 'slideCoverMobile',
                tablet: 'slideCoverTablet',
                desktop: 'slideCover'
            }

            if (this.mediaType(media.mimeType) === 'image') {
                this.setSlideProperty({
                    key: `${mappedViewportFields[this.deviceView]}Id`,
                    value: media.id
                })
                this.setSlideProperty({
                    key: mappedViewportFields[this.deviceView],
                    value: media
                })
            } else {
                console.warn('media must be an image')
            }
        },

        setSlideCoverVideo (media: any) {
            if (this.mediaType(media.mimeType) === 'video') {
                this.setSlideProperty({
                    key: 'slideCoverVideoId',
                    value: media.id
                })
                this.setSlideProperty({
                    key: 'slideCoverVideo',
                    value: media
                })
            } else {
                console.warn('media must be an image')
            }
        },

        setFocusImage (media: any) {
            if (this.mediaType(media.mimeType) === 'image') {
                this.setSlideProperty({
                    key: 'presentationMediaId',
                    value: media.id
                })
                this.setSlideProperty({
                    key: 'presentationMedia',
                    value: media
                })
            } else {
                console.warn('media must be an image')
            }
        },

        mediaType (mimeType: string) {
            return mimeType.split('/')[0]
        }
    },

    created () {
        if (this.newSlide === true) {
            this.createSlide()
        } else {
            this.loadSlide()
            this.loadCustomFieldSets()
        }
    }
})