const { Mixin, Component } = Shopware;
const { mapState, mapMutations } = Component.getComponentHelper()

// give the mixin a name and feed it into the register function as the second argument
export default Mixin.register('blur-device-utilities', Component.wrapComponentConfig({


    data () {
        return {
            /**
             * @deprecated The `deviceStyle` methoded will be moved to its own mixin and is renamned to `viewStyle`
             * The dependened methods or properties will be removed from this mixin
             */
            currentBreakpoint: 'mobile',
            viewportsSettings: null
        }
    },

    computed: {

        ...mapState('blurElysiumSlide', [
            'deviceView'
        ]),

        ...mapState('adminMenu', [
            'isExpanded'
        ]),

        /**
         * @deprecated The `deviceStyle` methoded will be moved to its own mixin and is renamned to `viewStyle`
         * The dependened methods or properties will be removed from this mixin
         */
        slideViewportSettings () {
            return this.slide.slideSettings.viewports[this.deviceView]
        },

        /**
         * @deprecated to initalize the viewport settings set your object to viewportsSettings in your component
         */
        slideViewportsSettings () {
            return this.slide.slideSettings.viewports
        },

        /**
         * @deprecated since the change from devicePlaceholder to viewportsPlaceholder this computed property is not used anymore
         */
        currentDeviceIndex () {
            return Object.entries(this.slideViewportsSettings).findIndex(element => element[0] === this.deviceView)
        },

        currentViewportIndex () {
            return Object.entries(this.viewportsSettings).findIndex(element => element[0] === this.deviceView)
        },
    },

    watch: {
        /**
         * @deprecated The `deviceStyle` methoded will be moved to its own mixin and is renamned to `viewStyle`
         * The dependened methods or properties will be removed from this mixin
         */
        isExpanded() {
            this.setCurrentBreakpoint()
        }
    },

    mounted () {
        /**
         * @deprecated The `deviceStyle` methoded will be moved to its own mixin and is renamned to `viewStyle`
         * The dependened methods or properties will be removed from this mixin
         */
        this.setCurrentBreakpoint()
    },

    methods: {

        ...mapMutations('blurElysiumSlide', [
            'setDeviceView'
        ]),

        /**
         * @deprecated The `deviceStyle` methoded will be moved to its own mixin and is renamned to `viewStyle`
         * The dependened methods or properties will be removed from this mixin
         */
        setCurrentBreakpoint () {

            const breakpointListeners = {
                mobile: '(min-width: 0px) and (max-width: 767px)',
                tablet: '(min-width: 768px) and (max-width: 1199px)',
                desktop: '(min-width: 1200px)'
            }

            if (this.isExpanded === true) {
                breakpointListeners.mobile = '(min-width: 0px) and (max-width: 1023px)'
                breakpointListeners.tablet = '(min-width: 1024px) and (max-width: 1399px)'
                breakpointListeners.desktop = '(min-width: 1400px)'
            }
    
            Object.entries(breakpointListeners).forEach(([view, mediaQuery]) => {
                const matchMedia = window.matchMedia(mediaQuery)
    
                if (matchMedia.matches) {
                    this.currentBreakpoint = view
                }
    
                matchMedia.addEventListener('change', event => event.matches && (this.currentBreakpoint = view))
            })
        }, 

        deviceSwitch (device: string) {
            if (device === "desktop") {
                this.setDeviceView("mobile");
            } else if (device === "mobile") {
                this.setDeviceView("tablet");
            } else if (device === "tablet") {
                this.setDeviceView("desktop");
            }
        },

        viewportsPlaceholder(property: string, fallback: string|number, snippetPrefix: string|null = null) {

            const kebabToCamelCase = (string: string) => {
                return string.split('-').map((word, index) => {
                    return index === 0 ? word : word.charAt(0).toUpperCase() + word.slice(1);
                }).join('');
            }

            let placeholder: string|number = fallback

            Object.values(this.viewportsSettings).forEach((settings, index) => {
                const settingValue: string|number|undefined|null = <string|number|undefined|null>property.split('.').reduce((r, k) => r?.[k], settings)
                if (!(settingValue === null || settingValue === undefined) && this.currentViewportIndex >= index) {
                    placeholder = settingValue
                }
            })

            if (typeof snippetPrefix === 'string' && typeof placeholder === 'string') {
                const snippet = [kebabToCamelCase(placeholder)]
                snippet.unshift(snippetPrefix)
                return this.$t(snippet.join("."))
            }

            return placeholder
        },

        /**
         * @deprecated because this method is bound to the computed property slideViewportsSettings it will be replaced by viewportsPlaceholder
         */
        devicePlaceholder(property: string, fallback: string|number, snippetPrefix: string|null = null) {

            const kebabToCamelCase = (string: string) => {
                return string.split('-').map((word, index) => {
                    return index === 0 ? word : word.charAt(0).toUpperCase() + word.slice(1);
                }).join('');
            }

            let placeholder: string|number = fallback

            Object.values(this.slideViewportsSettings).forEach((settings, index) => {
                const settingValue: string|number|undefined|null = <string|number|undefined|null>property.split('.').reduce((r, k) => r?.[k], settings)
                if (!(settingValue === null || settingValue === undefined) && this.currentDeviceIndex >= index) {
                    placeholder = settingValue
                }
            })

            if (typeof snippetPrefix === 'string' && typeof placeholder === 'string') {
                const snippet = [kebabToCamelCase(placeholder)]
                snippet.unshift(snippetPrefix)
                return this.$t(snippet.join("."))
            }

            return placeholder
        },

        /**
         * @deprecated The `deviceStyle` methoded will be moved to its own mixin and is renamned to `viewStyle`
         * The dependened methods or properties will be removed from this mixin
         */
        deviceStyle( responsiveStyles: object ) {
            const mergedStyles = Object.assign({
                mobile: {},
                tablet: {},
                desktop: {}
            }, responsiveStyles)

            const currentBreaktpointIndex = Object.entries(mergedStyles).findIndex(element => element[0] === this.currentBreakpoint)

            const accumulatedStyles = Object.values(mergedStyles).reduce((acc, value, index) => {
                if (index <= currentBreaktpointIndex) {
                    return { ...acc, ...value };
                }
                return acc;
            }, {});

            return accumulatedStyles
        }
    }
}));