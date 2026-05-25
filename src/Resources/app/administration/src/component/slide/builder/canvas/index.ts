import template from './template.html.twig'

const { Component, Mixin, Store, Context, Data } = Shopware;
const { Criteria } = Data;

export default Component.wrapComponentConfig({
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('blur-style-utilities'),
    ],

    data() {
        return {
            styles: {
                container: {
                    mobile: {
                        'position': 'relative',
                        'display': 'flex',
                        'flex-direction': 'column',
                        'padding': '0px',
                        'width': '100%',
                        'overflow-y': 'auto',
                    }
                },
                canvas: {
                    mobile: {
                        'flex': '1 0%',
                        'display': 'flex',
                        'flex-direction': 'row',
                        'flex-wrap': 'wrap',
                        'justify-content': 'center',
                        'align-items': 'center',
                        'margin': '0 auto',
                        'width': '100%',
                        'height': '100%',
                    }
                }
            }
        };
    },

    computed: {

        elysiumSlide() {
            return Store.get('elysiumSlide');
        },

        elysiumUI() {
            return Store.get('elysiumUI');
        },

        slide() {
            return this.elysiumSlide.slide;
        },

        device() {
            return Store.get('elysiumUI').device;
        },

        aspectRatioX() {
            return this.elysiumUI.previewSettings[this.device].aspectRatioX;
        },

        aspectRatioY() {
            return this.elysiumUI.previewSettings[this.device].aspectRatioY;
        },

        width() {
            return this.elysiumUI.previewSettings[this.device].width;
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

        hasAvailableDomains() {
            return this.salesChannels.some((channel) => channel.domains && channel.domains.length > 0);
        },

        salesChannelRepository() {
            return this.repositoryFactory.create('sales_channel');
        },
    },

    methods: {
        onAspectRatioXChange(value: number) {
            this.elysiumUI.setPreviewAspectRatioX(this.device, value);
        },

        onAspectRatioYChange(value: number) {
            this.elysiumUI.setPreviewAspectRatioY(this.device, value);
        },

        onWidthChange(value: number | null) {
            this.elysiumUI.setPreviewWidth(this.device, value);
        },

        loadSalesChannels() {
            const criteria = new Criteria();
            criteria.addAssociation('domains');
            criteria.addFilter(Criteria.equals('typeId', Shopware.Defaults.storefrontSalesChannelTypeId));

            this.salesChannelRepository.search(criteria, Context.api)
                .then((result) => {
                    const channels = result;
                    this.elysiumUI.setSalesChannels(channels);

                    // Auto-select first channel with domains if nothing selected
                    if (!this.selectedSalesChannelId) {
                        const firstChannel = channels.find((channel) => channel.domains && channel.domains.length > 0);
                        if (firstChannel) {
                            this.elysiumUI.setSelectedSalesChannelId(firstChannel.id);
                            this.resolveAndSetDomain();
                        }
                    }
                })
                .catch((error) => {
                    console.error('Failed to load sales channels', error);
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
