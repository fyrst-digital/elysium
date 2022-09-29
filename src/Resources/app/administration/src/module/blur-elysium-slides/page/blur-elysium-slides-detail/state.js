export default {
    namespaced: true,
    state() {
        return {
            apiContext: {},
            slide: {},
            localMode: false,
            loading: {
                slide: false
            }
        }
    },
    mutations: {
        setApiContext(state, apiContext) {
            state.apiContext = apiContext;
        },
        setSlide(state, newSlide) {
            state.slide = newSlide;
        },
        setLocalMode(state, value) {
            state.localMode = value;
        },
        setLoading(state, value) {
            const name = value[0];
            const data = value[1];

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