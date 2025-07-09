import Swiper from 'swiper'
import deepmerge from 'deepmerge'

const { PluginBaseClass } = window

export default class ElysiumSlider extends PluginBaseClass {
    swiper = null;

    /**
     * default slider options
     *
     * @type {*}
     */
    static options = {
        swiperSelector: '[data-elysium-slider-swiper]'
    };

    init() {
        const inlineOptions = typeof this.el.dataset.elysiumSlider === 'string' ? JSON.parse(this.el.dataset.elysiumSlider) : {}
        const swiperElement = this.el.querySelector(this.options.swiperSelector)
        this.swiper = new Swiper(swiperElement, deepmerge({
            a11y: true,
        }, inlineOptions))
        console.log('ElysiumSlider initialized', inlineOptions)
    }
}