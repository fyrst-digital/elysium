import Swiper from 'swiper'
import { A11y, Autoplay, EffectFade, Keyboard, Navigation, Pagination } from 'swiper/modules'
import deepmerge from 'deepmerge'
import { syncOffscreenSlides } from './utils/offscreen-slides'
import { applyReducedMotion, pauseCoverVideos } from './utils/reduced-motion'

const { PluginBaseClass } = window

export default class ElysiumSlider extends PluginBaseClass {
    /**
     * default slider options
     *
     * @type {*}
     */
    static options = {
        swiperSelector: '[data-elysium-slider-swiper]',
        autoplayToggleSelector: '[data-elysium-slider-autoplay]',
    };

    init() {
        const inlineOptions = typeof this.el.dataset.swiperOptions === 'string' ? JSON.parse(this.el.dataset.swiperOptions) : {}
        const swiperElement = this.el.querySelector(this.options.swiperSelector)
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches

        if (prefersReducedMotion) {
            pauseCoverVideos(this.el)
        }

        const options = applyReducedMotion(deepmerge({
            watchSlidesProgress: true,
            on: {
                init: this.onSlideInit.bind(this),
                paginationRender: this._ensureBulletButtonType.bind(this),
            },
            keyboard: {
                enabled: true,
                pageUpDown: false,
            },
        }, inlineOptions), prefersReducedMotion)

        options.modules = [A11y, Autoplay, Navigation, Pagination, EffectFade, Keyboard]

        // Assign on `this` only. A class field would reset after PluginBaseClass.init().
        this.swiper = new Swiper(swiperElement, options)
        this.autoplayToggle = this.el.querySelector(this.options.autoplayToggleSelector)

        if (prefersReducedMotion && this.autoplayToggle) {
            this.autoplayToggle.hidden = true
        }

        this.listeners()
    }

    listeners() {
        this.swiper.on('slideChange', this.onSlideChange.bind(this))
        this.swiper.on('breakpoint', this.onSlideChange.bind(this))
        this.swiper.on('resize', this.onSlideChange.bind(this))
        this.swiper.on('autoplayStart', this.onAutoplayStateChange.bind(this))
        this.swiper.on('autoplayStop', this.onAutoplayStateChange.bind(this))
        this.swiper.on('autoplayPause', this.onAutoplayStateChange.bind(this))
        this.swiper.on('autoplayResume', this.onAutoplayStateChange.bind(this))

        if (this.autoplayToggle) {
            this.autoplayToggle.addEventListener('click', this.onAutoplayToggle.bind(this))
        }

        this.$emitter.publish('listeners', { swiper: this.swiper })
    }

    onSlideInit(swiper) {
        syncOffscreenSlides(swiper.slides)
        this._syncAutoplayControl()

        this.$emitter.publish('onSlideInit', { swiper })
    }

    onSlideChange(swiper) {
        syncOffscreenSlides(swiper.slides)

        this.$emitter.publish('onSlideChange', { swiper })
    }

    onAutoplayToggle() {
        if (!this.swiper?.autoplay) {
            return
        }

        if (this._isAutoplayRunning()) {
            this.swiper.autoplay.pause()
        } else if (this.swiper.autoplay.paused) {
            this.swiper.autoplay.resume()
        } else {
            this.swiper.autoplay.start()
        }

        this._syncAutoplayControl()
    }

    onAutoplayStateChange() {
        this._syncAutoplayControl()
    }

    _ensureBulletButtonType(swiper) {
        swiper.pagination?.bullets?.forEach((bullet) => {
            if (bullet.tagName === 'BUTTON') {
                bullet.setAttribute('type', 'button')
            }
        })
    }

    _isAutoplayRunning() {
        return Boolean(this.swiper?.autoplay?.running && !this.swiper.autoplay?.paused)
    }

    _syncAutoplayControl() {
        if (!this.autoplayToggle) {
            return
        }

        const running = this._isAutoplayRunning()
        const pauseLabel = this.autoplayToggle.dataset.labelPause
        const playLabel = this.autoplayToggle.dataset.labelPlay
        const pauseIcon = this.autoplayToggle.querySelector('[data-elysium-slider-autoplay-icon="pause"]')
        const playIcon = this.autoplayToggle.querySelector('[data-elysium-slider-autoplay-icon="play"]')

        this.autoplayToggle.setAttribute('aria-pressed', running ? 'true' : 'false')
        this.autoplayToggle.setAttribute('aria-label', running ? pauseLabel : playLabel)

        if (pauseIcon) {
            pauseIcon.hidden = !running
        }

        if (playIcon) {
            playIcon.hidden = running
        }
    }
}
