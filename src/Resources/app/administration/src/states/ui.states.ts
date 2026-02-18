type Device = 'mobile' | 'tablet' | 'desktop';

interface UIState {
    device: Device;
    mediaSidebar: HTMLElement | null;
}

export default {
    id: 'elysiumUI',

    state: (): UIState => ({
        device: 'desktop',
        mediaSidebar: null,
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
    },
};
