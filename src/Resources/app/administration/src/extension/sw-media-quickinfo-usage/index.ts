import { module } from 'blurElysium/meta'
import template from './template.html.twig'
import './style.scss'

const { Data, Context } = Shopware
const { Criteria } = Data

/**
 * @todo
 * This component needs styling
 */

export default {
    template,
    inject: [
        'repositoryFactory'
    ],
    data() {

        return {
            elysiumSlidesUsageCollection: {},
        };
    },

    computed: {
        elysiumSlidesRepository () {
            return this.repositoryFactory.create('blur_elysium_slides')
        },
        elysiumSlidesCriteria () {
            const criteria = new Criteria()
            criteria.addFilter(
                Criteria.multi('or', [
                    Criteria.equals('slideCoverId', this.item.id),
                    Criteria.equals('slideCoverMobileId', this.item.id),
                    Criteria.equals('slideCoverTabletId', this.item.id),
                    Criteria.equals('slideCoverVideoId', this.item.id),
                    Criteria.equals('presentationMediaId', this.item.id)
                ])
            )
            return criteria
        },
        getElysiumSlidesUsages () {
            const originalUsages = this.$options.computed.getUsages.call(this)

            return originalUsages
        },
        elysiumIconColor () {
            return module.color
        }
    },

    methods: {
        loadElysiumSlidesAssociations() {
            this.elysiumSlidesRepository.search(
                this.elysiumSlidesCriteria,
                Context.api
            ).then((slides: any) => {
                this.elysiumSlidesUsageCollection = slides
                this.isLoading = false
                console.log(this.elysiumSlidesUsageCollection)
            }).catch((exception) => {
                console.warn(exception)
                this.isLoading = false
            })
        }
    },

    watch: {
        item() {
            this.isLoading = true
            this.loadElysiumSlidesAssociations();
        },
    },

    created() {
        this.isLoading = true
        this.loadElysiumSlidesAssociations()
    }
};