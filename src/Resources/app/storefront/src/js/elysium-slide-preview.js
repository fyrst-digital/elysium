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
            { path: 'container.maxWidth', prop: '--container-max-width', unit: 'px', zeroValue: 'auto' },
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
            { path: 'image.maxWidth', prop: '--image-max-w', unit: 'px', zeroValue: 'auto' },
            { path: 'image.justifyContent', prop: '--image-justify-content' },
            { path: 'content.textAlign', prop: '--content-text-align' },
            { path: 'content.paddingX', prop: '--content-padding-x', unit: 'px' },
            { path: 'content.paddingY', prop: '--content-padding-y', unit: 'px' },
            { path: 'content.maxWidth', transform: (v, styles) => {
                if (v === 0) {
                    styles['--content-flex-basis'] = '0%';
                    styles['--content-max-w'] = 'auto';
                    styles['--content-flex-grow'] = '1';
                } else {
                    styles['--content-flex-basis'] = `${v}px`;
                    styles['--content-max-w'] = `${v}px`;
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

    updateTextContent(slide) {
        const descriptionEl = document.querySelector('[data-elysium-slide-description]');
        if (descriptionEl && slide.description !== undefined) {
            descriptionEl.innerHTML = slide.description;
        }
    }

    async _fetchPartial(partial, slide) {
        const url = `/elysium-slide/preview/${partial}/${this.slideId}`;
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

    async updateHeadline(slide) {
        try {
            const html = await this._fetchPartial('headline', slide);
            const existingEl = document.querySelector('[data-elysium-slide-headline]');
            if (existingEl) {
                if (html.trim()) {
                    existingEl.outerHTML = html;
                } else {
                    existingEl.remove();
                }
            } else if (html.trim()) {
                const contentEl = document.querySelector('.blur-elysium-slide-content');
                if (contentEl) {
                    contentEl.insertAdjacentHTML('afterbegin', html);
                }
            }
        } catch (err) {
            this._showError('Failed to update headline preview');
            console.error(err);
        }
    }

    async updateButton(slide) {
        try {
            const html = await this._fetchPartial('button', slide);
            const existingActions = document.querySelector('.blur-elysium-slide-actions');
            if (existingActions) {
                if (html.trim()) {
                    existingActions.outerHTML = html;
                } else {
                    existingActions.remove();
                }
            } else if (html.trim()) {
                const contentEl = document.querySelector('.blur-elysium-slide-content');
                if (contentEl) {
                    contentEl.insertAdjacentHTML('beforeend', html);
                }
            }
        } catch (err) {
            this._showError('Failed to update button preview');
            console.error(err);
        }
    }

    async updateCoverMedia(slide, element) {
        try {
            const html = await this._fetchPartial('cover', slide);
            const existingPicture = element.querySelector('.blur-elysium-slide-cover-picture');
            const existingVideo = element.querySelector('.blur-elysium-slide-cover-video');

            if (existingPicture) existingPicture.remove();
            if (existingVideo) existingVideo.remove();

            if (html.trim()) {
                element.insertAdjacentHTML('afterbegin', html);
            }
        } catch (err) {
            this._showError('Failed to update cover preview');
            console.error(err);
        }
    }

    async updateFocusImage(slide) {
        try {
            const html = await this._fetchPartial('focus-image', slide);
            const existingEl = document.querySelector(
                `[data-elysium-slide-focus-image="${this.slideId}"]`
            );

            if (existingEl) {
                if (html.trim()) {
                    existingEl.outerHTML = html;
                } else {
                    existingEl.remove();
                }
            } else if (html.trim()) {
                const container = document.querySelector(
                    `[data-elysium-slide-container="${this.slideId}"]`
                );
                if (container) {
                    container.insertAdjacentHTML('beforeend', html);
                }
            }
        } catch (err) {
            this._showError('Failed to update focus image preview');
            console.error(err);
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

    async updateSlide(data) {
        const slide = data.slide;
        const device = data.device || 'desktop';
        const element = this.el;

        if (!element || !slide) {
            return;
        }

        this._clearManagedStyles(element);
        this.updateBaseStyles(slide, element);
        this.updateViewportStyles(slide, device, element);
        this.updateCssClass(slide);
        this.updateTextContent(slide);

        await Promise.all([
            this.updateHeadline(slide),
            this.updateButton(slide),
            this.updateCoverMedia(slide, element),
            this.updateFocusImage(slide),
        ]);
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
