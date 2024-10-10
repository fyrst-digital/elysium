const { Mixin, Component } = Shopware;
const { mapState, mapMutations } = Component.getComponentHelper()

// give the mixin a name and feed it into the register function as the second argument
export default Mixin.register('blur-style-utilities', Component.wrapComponentConfig({

    data () {
        return {
            currentView: 'mobile',
        }
    },

    computed: {

        ...mapState('adminMenu', [
            'isExpanded'
        ]),
    },

    watch: {
        isExpanded() {
            this.setCurrentView()
        }
    },

    mounted () {
        this.setCurrentView()
    },

    methods: {

        ...mapMutations('blurElysiumSlide', [
            'setDeviceView'
        ]),

        setCurrentView () {

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
                    this.currentView = view
                }
    
                matchMedia.addEventListener('change', event => event.matches && (this.currentView = view))
            })
        }, 

        viewStyle( responsiveStyles: object ) {
            const mergedStyles = Object.assign({
                mobile: {},
                tablet: {},
                desktop: {}
            }, responsiveStyles)

            
            const currentBreaktpointIndex = Object.entries(mergedStyles).findIndex(element => element[0] === this.currentView)
            
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