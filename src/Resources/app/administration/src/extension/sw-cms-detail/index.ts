import ShopwareError from "src/core/data/ShopwareError";

const { Component } = Shopware;

/**
 * Override sw-cms-detail component
 * Includes the onSaveEntity function copied from Shopware's original component
 */
export default Component.wrapComponentConfig({
    methods: {
        /**
         * Save the CMS page entity
         * Copied from Shopware's sw-cms-detail component
         */
        onSaveEntity() {
            this.isLoading = true;
            this.deleteEntityAndRequiredConfigKey(this.page.sections);

            return this.pageRepository
                .save(this.page, Shopware.Context.api, false)
                .then(() => {
                    this.isLoading = false;
                    this.isSaveSuccessful = true;

                    return this.loadPage(this.page.id);
                })
                .catch((exception) => {
                    const errors = exception.response?.data?.errors || [];
                    this.isLoading = false;

                    if (errors.length > 0) {
                        this.gracefulErrorHandling(errors);
                    } else {
                        this.createNotificationError({
                            message: exception.message,
                        });
                    }


                    return Promise.reject(exception);
                });
        },

        gracefulErrorHandling(errors: ShopwareError[]) {

            if (errors.length <= 0) {
                return;
            }

            console.log("Handling errors gracefully:", errors);

            errors.forEach(error => {
                this.createNotificationError({
                    message: error.code,
                });
            });
        },
    },
});
