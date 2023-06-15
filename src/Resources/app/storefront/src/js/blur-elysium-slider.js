import deepmerge from 'deepmerge';
import Plugin from 'src/plugin-system/plugin.class';
import PluginManager from 'src/plugin-system/plugin.manager';
import Splide from '@splide';

export default class BlurElysiumSlider extends Plugin {

    slider;

    /**
     * default slider options
     *
     * @type {*}
     */
    static options = {
        splideSelector: null,
        splideOptions: {
            classes: {
                page: "splide__pagination__page blur-esldr__nav-bullet",
            },
            pagination: true,
            omitEnd: true
        }
    };

    init() {
        let splideSelector = this.el
        let inlineOptions = null
    
        if ( this.options.splideSelector !== null ) {
            splideSelector = this.el.querySelector(this.options.splideSelector)
        }

        if (typeof this.el.dataset.blurElysiumSlider === "string" ) {
            inlineOptions = JSON.parse( this.el.dataset.blurElysiumSlider )
            this.options = deepmerge(this.options, inlineOptions)
            console.dir(this.options)
        }


        // init slider with class property without mounting it
        this.setSlider(new Splide( splideSelector, this.options.splideOptions))
        // mount the slider
        this.getSlider().mount()
    }

    setSlider(slider) {
        this.slider = slider;
    }

    getSlider() {
        return this.slider;
    }
}