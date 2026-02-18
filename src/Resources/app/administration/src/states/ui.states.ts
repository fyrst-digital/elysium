interface UIState {
    device: string;
    mediaSidebar: unknown;
}

export default {
    id: 'elysiumUI',

    state: (): UIState => ({
        device: 'desktop',
        mediaSidebar: null,
    }),

    actions: {
        setDevice(device: string) {
            this.device = device;
        },

        resetDevice() {
            this.device = 'desktop';
        },

        setMediaSidebar(element: unknown) {
            this.mediaSidebar = element;
        },
    },
};
