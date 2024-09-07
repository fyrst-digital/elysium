export default {
    namespaced: true,

    state() {
        return {
            slide: null,
            customFieldSet: null,
            mediaSidebar: null,
            deviceView: 'desktop'
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
        setDeviceView(state, deviceView) {
            state.deviceView = deviceView;
        },
    }
};