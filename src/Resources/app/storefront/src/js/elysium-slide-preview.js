import { previewSchema, getFragmentsForFields } from './elysium-preview-schema-slide';
import { createFragmentRenderers } from './elysium-preview-fragment-renderers';

const { PluginBaseClass } = window;

export default class ElysiumSlidePreview extends PluginBaseClass {
    static options = {
        allowedOrigin: [],
    };

    _styleMaps = {
        base: [
            { path: 'slideSettings.slide.bgColor', prop: '--slide-bg-color' },
            { path: 'slideSettings.slide.bgGradient.startColor', prop: '--slide-gradient-color-start', transform: (v) => v ?? 'transparent' },
            { path: 'slideSettings.slide.bgGradient.endColor', prop: '--slide-gradient-color-end', transform: (v) => v ?? 'transparent' },
            { path: 'slideSettings.slide.bgGradient.gradientDeg', prop: '--slide-gradient-deg', transform: (v) => v != null ? `${v}deg` : undefined },
            { path: 'slideSettings.container.bgColor', prop: '--container-bg-color' },
            { path: 'slideSettings.slide.headline.color', prop: '--headline-color' },
            { path: 'slideSettings.slide.description.color', prop: '--description-color' },
        ],
        viewport: [
            { path: 'slide.paddingX', prop: '--slide-padding-x', unit: 'px' },
            { path: 'slide.paddingY', prop: '--slide-padding-y', unit: 'px' },
            { path: 'slide.alignItems', prop: '--slide-align-items' },
            { path: 'slide.justifyContent', prop: '--slide-justify-content' },
            { path: 'slide.borderRadius', prop: '--slide-border-radius', unit: 'px' },
            { path: 'container.gap', prop: '--container-gap', unit: 'px' },
            { path: 'container.paddingX', prop: '--container-padding-x', unit: 'px' },
            { path: 'container.paddingY', prop: '--container-padding-y', unit: 'px' },
            { path: 'container.borderRadius', prop: '--container-border-radius', unit: 'px' },
            { path: 'container.alignItems', prop: '--container-align-items' },
            { path: 'container.justifyContent', prop: '--container-justify-content' },
            { path: 'container.basis', prop: '--container-max-width', unit: '%', zeroValue: 'auto' },
            { path: 'container.columnWrap', prop: '--container-direction', transform: (v) => v === true ? 'column' : 'row' },
            { path: 'container.order', transform: (v, styles) => {
                if (v === 'reverse') {
                    styles['--image-order'] = '1';
                    styles['--content-order'] = '2';
                } else if (v === 'default') {
                    styles['--image-order'] = '2';
                    styles['--content-order'] = '1';
                }
            }},
            { path: 'image.basis', prop: '--image-max-w', unit: '%', zeroValue: 'auto' },
            { path: 'image.justifyContent', prop: '--image-justify-content' },
            { path: 'content.textAlign', prop: '--content-text-align' },
            { path: 'content.paddingX', prop: '--content-padding-x', unit: 'px' },
            { path: 'content.paddingY', prop: '--content-padding-y', unit: 'px' },
            { path: 'content.basis', transform: (v, styles) => {
                if (v === 0) {
                    styles['--content-flex-basis'] = '0%';
                    styles['--content-max-w'] = 'auto';
                    styles['--content-flex-grow'] = '1';
                } else {
                    styles['--content-flex-basis'] = `${v}%`;
                    styles['--content-max-w'] = `${v}%`;
                    styles['--content-flex-grow'] = '0';
                }
            }},
            { path: 'headline.fontSize', prop: '--headline-font-size', unit: 'px' },
            { path: 'description.fontSize', prop: '--description-font-size', unit: 'px' },
            { path: 'coverMedia', wildcard: true, prefix: '--cover-' },
        ],
    };

    _managedProps = (() => {
        const props = new Set();

        const collect = (mappings) => {
            mappings.forEach((mapping) => {
                if (mapping.wildcard) return;
                if (mapping.prop) props.add(mapping.prop);
            });
        };

        collect(this._styleMaps.base);
        collect(this._styleMaps.viewport);

        ['--image-order', '--content-order', '--content-flex-basis', '--content-max-w', '--content-flex-grow'].forEach((p) => props.add(p));

        return Array.from(props);
    })();

    _appliedCssClasses = [];

    init() {
        this.slideId = this.el.dataset.elysiumSlideId;
        const origins = this.options.allowedOrigin;
        this.allowedOrigins = Array.isArray(origins) && origins.length > 0
            ? origins
            : [window.location.origin];

        this.layout = this.options.layout || 'detail';
        this.schema = previewSchema;
        this.fragmentRenderers = createFragmentRenderers();

        if (this.layout === 'cms') {
            this.el.style.flex = '1 100%';
            this.el.style.width = '100%';
            this.el.style.height = '100%';
        }

        window.addEventListener('message', this._handleMessage.bind(this));
    }

    _getPath(obj, path) {
        return path.split('.').reduce((current, part) => current?.[part], obj);
    }

    _camelToKebab(key) {
        return key.replace(/[A-Z]/g, (m) => `-${m.toLowerCase()}`);
    }

    _applyStyleMap(source, mappings, styles) {
        if (!source || typeof source !== 'object') {
            return;
        }

        mappings.forEach((mapping) => {
            const value = this._getPath(source, mapping.path);
            if (value === undefined || value === null || value === '') {
                return;
            }

            if (mapping.wildcard) {
                if (typeof value !== 'object') return;
                Object.entries(value).forEach(([subKey, subValue]) => {
                    if (subValue !== undefined && subValue !== null && subValue !== '') {
                        styles[mapping.prefix + this._camelToKebab(subKey)] = subValue;
                    }
                });
                return;
            }

            if (mapping.transform) {
                const result = mapping.transform(value, styles);
                if (result !== undefined && mapping.prop) {
                    styles[mapping.prop] = result;
                }
                return;
            }

            if (mapping.unit) {
                if (mapping.zeroValue !== undefined && value === 0) {
                    styles[mapping.prop] = mapping.zeroValue;
                } else {
                    styles[mapping.prop] = `${value}${mapping.unit}`;
                }
                return;
            }

            if (mapping.prop) {
                styles[mapping.prop] = value;
            }
        });
    }

    _clearManagedStyles(element) {
        const serverStyle = document.querySelector(`#style-${this.slideId}`);
        if (serverStyle) {
            serverStyle.remove();
        }

        this._managedProps.forEach((prop) => {
            element.style.removeProperty(prop);
        });
    }

    _applyMappedStyles(source, mapper, element) {
        const styles = {};
        this._applyStyleMap(source, mapper, styles);
        Object.entries(styles).forEach(([prop, value]) => {
            element.style.setProperty(prop, value);
        });
    }

    updateBaseStyles(slide, element) {
        this._applyMappedStyles(slide, this._styleMaps.base, element);
    }

    updateViewportStyles(slide, device, element) {
        const viewports = slide.slideSettings?.viewports;
        if (!viewports) {
            return;
        }

        if (viewports.mobile) {
            this._applyMappedStyles(viewports.mobile, this._styleMaps.viewport, element);
        }
        if (device !== 'mobile' && viewports.tablet) {
            this._applyMappedStyles(viewports.tablet, this._styleMaps.viewport, element);
        }
        if (device === 'desktop' && viewports.desktop) {
            this._applyMappedStyles(viewports.desktop, this._styleMaps.viewport, element);
        }
    }

    updateCssClass(slide) {
        const element = this.el;
        if (!element) return;

        const cssClass = slide.slideSettings?.slide?.cssClass;
        const newClasses = (cssClass ?? '').trim().split(/\s+/).filter((c) => c.length > 0);
        const previousClasses = this._appliedCssClasses;

        previousClasses.forEach((cls) => {
            element.classList.remove(cls);
        });

        newClasses.forEach((cls) => {
            element.classList.add(cls);
        });

        this._appliedCssClasses = newClasses;
    }

    _updatePreviewSizing(data, element) {
        if (!element) return;

        if (this.layout === 'cms') {
            element.style.removeProperty('--slide-aspect-ratio');
            document.body.style.removeProperty('--frame-max-w');
            document.body.style.removeProperty('--frame-padding');
            document.body.style.removeProperty('margin');
            return;
        }

        if (data.previewAspectRatio) {
            element.style.setProperty('--slide-aspect-ratio', `${data.previewAspectRatio.x} / ${data.previewAspectRatio.y}`);
        } else {
            element.style.removeProperty('--slide-aspect-ratio');
        }

        if (data.previewWidth) {
            document.body.style.setProperty('--frame-max-w', `${data.previewWidth}px`);
            document.body.style.setProperty('--frame-padding', `${data.framePadding ?? 0}px`);
            document.body.style.margin = '0 auto';
        } else {
            document.body.style.removeProperty('--frame-max-w');
            document.body.style.removeProperty('--frame-padding');
            document.body.style.removeProperty('margin');
        }
    }

    async _fetchPartial(partial, slide) {
        const device = this.currentDevice || 'desktop';
        const url = `/elysium-preview/blur-elysium-slide/fragment/${partial}/${this.slideId}?device=${encodeURIComponent(device)}`;
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ slide }),
        });

        if (!response.ok) {
            throw new Error(`Partial ${partial} failed: ${response.status}`);
        }

        return response.text();
    }

    _showError(message) {
        const existing = this.el.querySelector('.blur-elysium-slide-preview-error');
        if (existing) existing.remove();

        const errorEl = document.createElement('div');
        errorEl.className = 'blur-elysium-slide-preview-error';
        errorEl.textContent = message;
        errorEl.style.cssText = 'position:absolute;top:8px;left:8px;right:8px;z-index:9999;padding:8px 12px;background:#dc3545;color:#fff;border-radius:4px;font-size:12px;font-family:sans-serif;';
        this.el.prepend(errorEl);
        setTimeout(() => errorEl.remove(), 5000);
    }

    async updateSlide(data) {
        const slide = data.slide;
        const device = data.device || 'desktop';
        this.currentDevice = device;
        const fields = data.fields || ['slide'];
        const element = this.el;

        if (!element || !slide) {
            return;
        }

        const fragments = getFragmentsForFields(fields);
        const promises = [];

        for (const fragment of fragments) {
            const renderer = this.fragmentRenderers.get(fragment.mode);
            if (!renderer) {
                console.warn(`No renderer found for fragment mode: ${fragment.mode}`);
                continue;
            }

            try {
                const result = renderer.render(fragment, data, element, this);
                if (result instanceof Promise) {
                    promises.push(result);
                }
            } catch (err) {
                this._showError(`Failed to render fragment "${fragment.name}"`);
                console.error(err);
            }
        }

        if (promises.length > 0) {
            await Promise.all(promises);
        }
    }

    async _handleMessage(event) {
        if (!this.allowedOrigins.includes(event.origin)) {
            return;
        }
        if (event.data?.type === 'elysium-slide-update') {
            await this.updateSlide(event.data);
        }
    }
}
