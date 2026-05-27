import { module } from '@elysium/meta';
import template from './template.html.twig';
import './style.scss';

const { Data, Context } = Shopware;
const { Criteria } = Data;

/**
 * @todo
 * Media IDs are now stored in the contentSettings JSON field.
 * DAL filtering on JSON sub-keys is not straightforward.
 * This extension currently cannot efficiently find slides by media ID.
 * Consider implementing a custom SQL-based approach or removing this extension.
 */

export default {
    template,
    inject: ['repositoryFactory'],
    data() {
        return {
            elysiumSlidesUsageCollection: {},
        };
    },

    computed: {
        elysiumSlidesRepository() {
            return this.repositoryFactory.create('blur_elysium_slides');
        },
        elysiumSlidesCriteria() {
            // Media IDs are now in contentSettings JSON - cannot filter with simple equals
            // Return empty criteria to avoid incorrect results
            const criteria = new Criteria();
            criteria.setIds(['00000000000000000000000000000000']); // no results
            return criteria;
        },
        getElysiumSlidesUsages() {
            const originalUsages = this.$options.computed.getUsages.call(this);

            return originalUsages;
        },
        elysiumIconColor() {
            return module.color;
        },
    },

    methods: {
        loadElysiumSlidesAssociations() {
            this.elysiumSlidesRepository
                .search(this.elysiumSlidesCriteria, Context.api)
                .then((slides) => {
                    this.elysiumSlidesUsageCollection = slides;
                    this.isLoading = false;
                })
                .catch((exception) => {
                    console.warn(exception);
                    this.isLoading = false;
                });
        },
    },

    watch: {
        item() {
            this.isLoading = true;
            this.loadElysiumSlidesAssociations();
        },
    },

    created() {
        this.isLoading = true;
        this.loadElysiumSlidesAssociations();
    },
};
