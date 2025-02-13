/**
 * @todo replace any with proper types
 */

interface UIState {
    device: string;
}

export default {

    id: 'elysiumUI',

    state: (): UIState => ({
        device: 'desktop',
    }),

    actions: {
        setDevice(device: string) {
            this.device = device;
        },

        resetDevice() {
            this.device = 'desktop';
        },
    },
}