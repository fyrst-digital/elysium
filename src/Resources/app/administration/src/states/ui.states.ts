/**
 * @todo replace any with proper types
 */

interface UIState {
    device: string;
    mediaSidebar: any;
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

        setMediaSidebar(element: any) {
            this.mediaSidebar = element;
        },
    },
}