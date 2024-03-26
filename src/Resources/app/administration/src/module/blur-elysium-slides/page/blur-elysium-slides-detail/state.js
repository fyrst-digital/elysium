const set = (t, path, value) => {
    if (typeof t !== 'object') return
    if (path === '') throw Error('empty path')

    const pos = path.indexOf('.')
    const paths = path.split('.')

    /**
     * @deprecated if path is undefined create empty object
     * useless since slideSettings object is deep merged anyway and the keys always exists
     * evaluate removal due to possible sideeffects
     */
    if (t[paths[0]] === undefined) {
        t[paths[0]] = {}
    }

    /* eslint-disable-next-line no-return-assign */
    return pos === -1
        ? (t[path] = value, value)
        : set(t[path.slice(0, pos)], path.slice(pos + 1), value)
}

export default {
    namespaced: true,
    state () {
        return {
            apiContext: {},
            slide: {},
            customFieldSets: [],
            /** @type 'mobile' | 'tablet' | 'desktop' */
            viewport: 'desktop',
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
        setApiContext (state, apiContext) {
            state.apiContext = apiContext
        },
        setSlide (state, slide) {
            state.slide = slide
        },
        setSlideProperty (state, payload) {
            state.slide[payload.key] = payload.value
        },
        setSlideSetting (state, payload) {
            set(state.slide.slideSettings, payload.key, payload.value)
        },
        setViewportSetting (state, payload) {
            set(state.slide.slideSettings.viewports[state.viewport], payload.key, payload.value)
        },
        setCustomFieldSets (state, value) {
            state.customFieldSets = value
        },
        setViewport (state, value) {
            state.viewport = value
        },
        setAcl (state, payload) {
            state.acl[payload.role] = payload.state
        },
        /**
         * @deprecated check if this method is nessecary. if not remove it
         */
        setLocalMode (state, value) {
            state.localMode = value
        },
        setMediaSidebar (state, value) {
            state.mediaSidebar = value
        },
        setLoading (state, payload) {
            const name = payload.key
            const data = payload.loading

            if (typeof data !== 'boolean') {
                return false
            }

            if (state.loading[name] !== undefined) {
                state.loading[name] = data
                return true
            }
            return false
        }
    }
}
