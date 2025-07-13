import Swiper from 'swiper'
import { Autoplay, Navigation, Pagination } from 'swiper/modules'
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
        const inlineOptions = typeof this.el.dataset.swiperOptions === 'string' ? JSON.parse(this.el.dataset.swiperOptions) : {}
        const swiperElement = this.el.querySelector(this.options.swiperSelector)
        this.swiper = new Swiper(swiperElement, deepmerge({
            a11y: true,
            observer: true,
            modules: [Autoplay, Navigation, Pagination],
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            },
        }, inlineOptions))
    }
}