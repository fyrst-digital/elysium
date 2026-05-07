type Device = 'mobile' | 'tablet' | 'desktop';

interface PreviewSettings {
    aspectRatioX: number;
    aspectRatioY: number;
    width: number | null;
}

interface UIState {
    device: Device;
    mediaSidebar: HTMLElement | null;
    previewSettings: Record<Device, PreviewSettings>;
    salesChannels: Entity<'sales_channel'>[];
    selectedSalesChannelId: string | null;
    previewDomain: string | null;
    isLoadingSalesChannels: boolean;
}

export default {
    id: 'elysiumUI',

    state: (): UIState => ({
        device: 'desktop',
        mediaSidebar: null,
        previewSettings: {
            mobile: { aspectRatioX: 1, aspectRatioY: 1, width: 360 },
            tablet: { aspectRatioX: 4, aspectRatioY: 3, width: 768 },
            desktop: { aspectRatioX: 16, aspectRatioY: 9, width: 1400 },
        },
        salesChannels: [],
        selectedSalesChannelId: null,
        previewDomain: null,
        isLoadingSalesChannels: false,
    }),

    actions: {
        setDevice(device: Device) {
            this.device = device;
        },

        resetDevice() {
            this.device = 'desktop';
        },

        setMediaSidebar(element: HTMLElement | null) {
            this.mediaSidebar = element;
        },

        setPreviewAspectRatioX(device: Device, value: number) {
            this.previewSettings[device].aspectRatioX = value;
        },

        setPreviewAspectRatioY(device: Device, value: number) {
            this.previewSettings[device].aspectRatioY = value;
        },

        setPreviewWidth(device: Device, value: number | null) {
            this.previewSettings[device].width = value;
        },

        setSalesChannels(channels: Entity<'sales_channel'>[]) {
            this.salesChannels = channels;
        },

        setSelectedSalesChannelId(id: string | null) {
            this.selectedSalesChannelId = id;
        },

        setPreviewDomain(domain: string | null) {
            this.previewDomain = domain;
        },

        setLoadingSalesChannels(loading: boolean) {
            this.isLoadingSalesChannels = loading;
        },
    },
};
