export default {
    namespaced: true,

    state() {
        return {
            slide: null,
            customFieldSet: null,
            mediaSidebar: null,
            currentDevice: 'desktop'
        };
    },

    mutations: {
        setSlide(state, slide) {
            state.slide = slide;
        },
        setSlideProperty (state, payload) {
            state.slide[payload.key] = payload.value
        },
        setCustomFieldSet(state, customFieldSet) {
            state.customFieldSet = customFieldSet;
        },
        setMediaSidebar (state, value) {
            state.mediaSidebar = value
        },
        setCurrentDevice (state, currentDevice) {
            state.currentDevice = currentDevice;
        },
    }
};