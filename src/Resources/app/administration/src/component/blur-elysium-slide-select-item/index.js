import template from './blur-elysium-slide-select-item.twig'
import './blur-elysium-slide-select-item.scss'

// eslint-disable-next-line no-undef
const { Data, Context } = Shopware
const { Criteria } = Data

export default {
    template,

    inject: [
        'repositoryFactory'
    ],

    props: {
        selectedSlide: {
            type: String
        }
    },

    data () {
        return {
            isLoading: true,
            allowedImageExtension: [
                'jpg', 'png', 'webp', 'avif', 'svg'
            ],
            allowedVideoExtension: [
                'mp4', 'webm'
            ],
            slideData: {},
            itemStyles: {
                backgroundColor: null,
                backgroundImage: null
            }
        }
    },

    watch: {
    },

    computed: {
        slideMedia () {
            if (this.slideData && this.slideData.media) {
                return this.slideData.media
            } else if (this.slideData && this.slideData.mediaPortrait) {
                return this.slideData.mediaPortrait
            }

            return null
        },

        elysiumSlidesRepository () {
            return this.repositoryFactory.create('blur_elysium_slides')
        },

        elysiumSlideCriteria () {
            const criteria = new Criteria()

            return criteria
        },

        itemStyle () {
            if (this.slideData?.slideSettings?.slideBgColor) {
                this.itemStyles.backgroundColor = this.slideData.slideSettings.slideBgColor
            }

            if (this.slideCover !== null) {
                if (this.slideCover.thumbnails?.length > 0) {
                    this.itemStyles.backgroundImage = `url( ${this.slideCover.thumbnails.last().url} )`
                } else {
                    this.itemStyles.backgroundImage = `url( ${this.slideCover.url} )`
                }
            }

            return this.itemStyles
        },

        slideCovers () {
            let covers = {}
            if (this.slideData.slideCoverMobile) {
                covers.mobile = this.slideData.slideCoverMobile
            }
            if (this.slideData.slideCoverTablet) {
                covers.tablet = this.slideData.slideCoverTablet
            }
            if (this.slideData.slideCover) {
                covers.desktop = this.slideData.slideCover
            }
            return covers
        },

        slideCover () {
            let array = Object.values(this.slideCovers)

            if (array.length > 0) {
                return array.reverse()[0]
            }

            return null
        }
    },

    created () {
        this.getElysiumSlide()
    },

    watch: {
        slideData () {
            console.log(Object.values(this.slideCovers).reverse()[0], this.slideCover)
        }
    },

    methods: {
        getElysiumSlide () {
            this.elysiumSlidesRepository.get(this.selectedSlide, Context.api, this.elysiumSlideCriteria).then((result) => {
                this.slideData = result
                this.isLoading = false
            })
        },

        positionUp () {
            this.$emit('position-up', this.selectedSlide)
        },

        positionDown () {
            this.$emit('position-down', this.selectedSlide)
        },

        editSlide () {
            this.$emit('edit-slide', this.selectedSlide)
        },

        removeSlide () {
            this.$emit('remove-slide', this.selectedSlide)
        },

        startDrag (event) {
            this.$emit('start-drag', this.selectedSlide, event, this.$refs.selectItem)
        },

        enterDrag (event) {
            this.$emit('enter-drag', this.selectedSlide, event, this.$refs.selectItem)
        },

        endDrag (event) {
            this.$emit('end-drag', this.selectedSlide, event, this.$refs.selectItem)
        },

        leaveDrag (event) {
            this.$emit('leave-drag', this.selectedSlide, event, this.$refs.selectItem)
        }
    }
}
