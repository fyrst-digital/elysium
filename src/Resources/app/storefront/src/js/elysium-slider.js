import Swiper from 'swiper'
import { Autoplay, Navigation, Pagination, EffectFade } from 'swiper/modules'
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
        swiperSelector: '[data-elysium-slider-swiper]',
        liveUpdateSelector: '[data-live-update]',
        currentSlidePlaceholder: '{current}',
        totalSlidesPlaceholder: '{total}',
    };

    init() {
        const inlineOptions = typeof this.el.dataset.swiperOptions === 'string' ? JSON.parse(this.el.dataset.swiperOptions) : {}
        const swiperElement = this.el.querySelector(this.options.swiperSelector)

        this.liveUpdateElement = this.el.querySelector(this.options.liveUpdateSelector)
        this.liveUpdateSnippet = this.liveUpdateElement ? this.liveUpdateElement.textContent.trim() : null

        this.swiper = new Swiper(swiperElement, deepmerge({
            a11y: true,
            watchSlidesProgress: true,
            on: {
                init: this.onSlideInit.bind(this),
            },
            loop: false,
            modules: [Autoplay, Navigation, Pagination, EffectFade],
            pagination: false,
        }, inlineOptions))

        this.listeners()
    }

    listeners() {
        this.swiper.on('slideChange', this.onSlideChange.bind(this));
        this.swiper.on('paginationUpdate', this.onPaginationUpdate.bind(this));

        this.$emitter.publish('listeners', { swiper: this.swiper });
    }

    onSlideInit(swiper) {
        this._buildliveUpdate(swiper);
        this._buildA11y(swiper);
        this._buildA11yBullets(swiper);

        this.$emitter.publish('onSlideInit', { swiper });
    }

    onSlideChange(swiper) {
        this._buildliveUpdate(swiper);
        this._buildA11y(swiper);

        this.$emitter.publish('onSlideChange', { swiper });
    }

    onPaginationUpdate(swiper) {
        this._buildA11yBullets(swiper);

        this.$emitter.publish('onPaginationUpdate', { swiper });
    }

    _buildA11y(swiper) {
        if (swiper.slides?.length > 0) {
            const activeSlide = swiper.slides[swiper.activeIndex];

            swiper.slides.forEach((slide) => {

                slide.removeAttribute('aria-current');

                if (slide.classList.contains('swiper-slide-visible')) {
                    slide.setAttribute('tabindex', '-1');
                    slide.removeAttribute('aria-hidden');
                } else {
                    slide.setAttribute('tabindex', '0');
                    slide.setAttribute('aria-hidden', 'true');
                }
            });

            if (activeSlide) {
                activeSlide.setAttribute('aria-current', 'true');
            }
        }
    }

    _buildA11yBullets(swiper) {
        if (swiper.pagination?.bullets?.length > 0) {
            swiper.pagination.bullets.forEach((bullet) => {
                bullet.setAttribute('role', 'button');
                bullet.setAttribute('tabindex', '0');

                if (bullet.classList.contains('swiper-pagination-bullet-active')) {
                    bullet.setAttribute('aria-current', 'true');
                } else {
                    bullet.removeAttribute('aria-current');
                }
            });
        }
    }

    _buildliveUpdate(swiper) {
        this.liveUpdateElement.textContent = this._setLiveUpdateSnippet(swiper.activeIndex + 1, swiper.slides.length);
    }

    _setLiveUpdateSnippet(currentSlide, slides) {
        return this.liveUpdateSnippet.replace(this.options.currentSlidePlaceholder, currentSlide).replace(this.options.totalSlidesPlaceholder, slides);
    }
}