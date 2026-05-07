import template from './template.html.twig'

const { Component, Store, Context, Data } = Shopware;
const { Criteria } = Data;

export default Component.wrapComponentConfig({
    template,

    inject: ['repositoryFactory'],

    computed: {
        elysiumUI() {
            return Store.get('elysiumUI');
        },

        salesChannels() {
            return this.elysiumUI.salesChannels;
        },

        selectedSalesChannelId() {
            return this.elysiumUI.selectedSalesChannelId;
        },

        previewDomain() {
            return this.elysiumUI.previewDomain;
        },

        isLoading() {
            return this.elysiumUI.isLoadingSalesChannels;
        },

        hasAvailableDomains() {
            return this.salesChannels.some((channel) => channel.domains && channel.domains.length > 0);
        },

        selectedSalesChannel() {
            return this.salesChannels.find((channel) => channel.id === this.selectedSalesChannelId) || null;
        },

        resolvedDomainUrl() {
            const channel = this.selectedSalesChannel;
            if (!channel || !channel.domains || channel.domains.length === 0) {
                return null;
            }

            const matched = channel.domains.find(
                (domain) => domain.languageId === channel.languageId && domain.currencyId === channel.currencyId
            );

            return matched?.url ?? channel.domains[0].url;
        },

        salesChannelRepository() {
            return this.repositoryFactory.create('sales_channel');
        },
    },

    methods: {
        loadSalesChannels() {
            if (this.isLoading || this.salesChannels.length > 0) {
                if (!this.isLoading) {
                    this.resolveAndSetDomain();
                }
                return;
            }

            this.elysiumUI.setLoadingSalesChannels(true);

            const criteria = new Criteria();
            criteria.addAssociation('domains');
            criteria.addFilter(Criteria.equals('typeId', Shopware.Defaults.storefrontSalesChannelTypeId));

            this.salesChannelRepository.search(criteria, Context.api)
                .then((result) => {
                    const channels = result;
                    this.elysiumUI.setSalesChannels(channels);

                    if (!this.selectedSalesChannelId) {
                        const firstChannel = channels.find((channel) => channel.domains && channel.domains.length > 0);
                        if (firstChannel) {
                            this.elysiumUI.setSelectedSalesChannelId(firstChannel.id);
                            this.resolveAndSetDomain();
                        }
                    } else {
                        this.resolveAndSetDomain();
                    }
                })
                .catch((error) => {
                    console.error('Failed to load sales channels', error);
                })
                .finally(() => {
                    this.elysiumUI.setLoadingSalesChannels(false);
                });
        },

        onSalesChannelChange(salesChannelId: string) {
            this.elysiumUI.setSelectedSalesChannelId(salesChannelId);
            this.resolveAndSetDomain();
        },

        resolveAndSetDomain() {
            this.elysiumUI.setPreviewDomain(this.resolvedDomainUrl);
        },
    },

    created() {
        this.loadSalesChannels();
    },
});
