import BaseSliderPlugin from 'src/plugin/slider/base-slider.plugin';
import { tns } from 'tiny-slider/src/tiny-slider.module';
import PluginManager from 'src/plugin-system/plugin.manager';

export default class BlurElysiumSlider extends BaseSliderPlugin {

    /**
     * initialize the slider
     *
     * @private
     */
    _initSlider() {
        this.el.classList.add(this.options.initializedCls);

        let sliderInlineSettings = JSON.parse( this.el.dataset.sliderSettings );

        const container = this.el.querySelector(this.options.containerSelector);
        const controlsContainer = this.el.querySelector(this.options.controlsSelector);
        const onInit = () => {
            PluginManager.initializePlugins();

            this.$emitter.publish('initSlider');
        };

        if (container) {
            if (this._sliderSettings.enabled) {
                container.style.display = '';
                this._slider = tns({
                    container,
                    controlsContainer,
                    onInit,
                    ...this._sliderSettings,
                    ...sliderInlineSettings
                });
            } else {
                container.style.display = 'none';
            }
        }

        this.$emitter.publish('afterInitSlider');
    }
}