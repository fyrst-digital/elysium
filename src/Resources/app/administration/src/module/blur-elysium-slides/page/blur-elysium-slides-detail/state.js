export default {
    namespaced: true,
    state() {
        return {
            apiContext: {},
            slide: {},
            media: {
                slideCover: null,
                slideCoverPortrait: null
            },
            customFieldSets: [],
            /**
             * @deprecated check if this method is nessecary. if not removee it
             */
            localMode: false,
            mediaSidebar: null,
            loading: {
                slide: false
            },
            acl: {
                viewer: false,
                editor: false,
                creator: false,
                deleter: false
            }
        }
    },
    mutations: {
        setApiContext(state, apiContext) {
            state.apiContext = apiContext;
        },
        setSlide(state, slide) {
            state.slide = slide;
        },
        setSlideProperty(state, payload) {
            state.slide[payload.key] = payload.value;
            console.log('setSlideProperty', payload.key, payload.value)
        },
        setSlideSetting(state, payload) {
            state.slide.slideSettings[payload.key] = payload.value
        },
        setSlideMedia(state, payload) {
            state.media[payload.key] = payload.value
        },
        setCustomFieldSets(state, value) {
            state.customFieldSets = value
        },
        setAcl(state, payload) {
            state.acl[payload.role] = payload.state;
        },
        /**
         * @deprecated check if this method is nessecary. if not remove it
         */
        setLocalMode(state, value) {
            state.localMode = value;
        },
        setMediaSidebar(state, value) {
            state.mediaSidebar = value;
        },
        setLoading(state, payload) {
            const name = payload.key;
            const data = payload.loading;

            if (typeof data !== 'boolean') {
                return false;
            }

            if (state.loading[name] !== undefined) {
                state.loading[name] = data;
                return true;
            }
            return false;
        },
    }
}