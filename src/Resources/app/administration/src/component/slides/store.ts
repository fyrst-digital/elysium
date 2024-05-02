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
        setSlide(state: any, slide: any) {
            state.slide = slide;
        },
        setSlideProperty (state: any, payload: any) {
            state.slide[payload.key] = payload.value
        },
        setCustomFieldSet(state: any, customFieldSet: any) {
            state.customFieldSet = customFieldSet;
        },
        setMediaSidebar (state: any, value: any) {
            state.mediaSidebar = value
        },
        setDeviceView(state: any, deviceView: string) {
            state.deviceView = deviceView;
        },
    }
};