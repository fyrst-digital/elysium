const { PluginBaseClass } = window;

export default class ElysiumSlidePreview extends PluginBaseClass {
    static options = {
        allowedOrigin: null,
    };

    init() {
        this.slideId = this.el.dataset.elysiumSlideId;
        this.allowedOrigin = this.el.dataset.elysiumSlidePreviewAllowedOrigin || this.options.allowedOrigin || window.location.origin;
        this._appliedCssClasses = [];

        this._styleMaps = {
            base: [
                { path: 'slideSettings.slide.bgColor', prop: '--slide-bg-color' },
                { path: 'slideSettings.slide.bgGradient.startColor', prop: '--slide-gradient-color-start', transform: (v) => v || 'transparent' },
                { path: 'slideSettings.slide.bgGradient.endColor', prop: '--slide-gradient-color-end', transform: (v) => v || 'transparent' },
                { path: 'slideSettings.slide.bgGradient.gradientDeg', prop: '--slide-gradient-deg', transform: (v) => v !== null && v !== undefined ? `${v}deg` : undefined },
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
                { path: 'content.maxWidth', prop: '--content-max-w', unit: 'px', zeroValue: 'auto' },
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

        this._managedProps = this._buildManagedProps();

        window.addEventListener('message', this._handleMessage.bind(this));
    }

    _buildManagedProps() {
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
    }

    _getPath(obj, path) {
        const parts = path.split('.');
        let current = obj;
        for (let i = 0; i < parts.length; i++) {
            if (current === null || current === undefined) return undefined;
            current = current[parts[i]];
        }
        return current;
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
                Object.keys(value).forEach((subKey) => {
                    const subValue = value[subKey];
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
        Object.keys(styles).forEach((prop) => {
            element.style.setProperty(prop, styles[prop]);
        });
    }

    updateBaseStyles(slide, element) {
        this._applyMappedStyles(slide, this._styleMaps.base, element);
    }

    updateViewportStyles(slide, device, element) {
        const viewports = slide.slideSettings && slide.slideSettings.viewports;
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

    updateHeadline(slide) {
        const headlineElement = (slide.slideSettings && slide.slideSettings.slide && slide.slideSettings.slide.headline && slide.slideSettings.slide.headline.element) || 'div';
        const title = slide.title || '';
        const existingEl = document.querySelector('[data-elysium-slide-headline]');
        const contentEl = document.querySelector('.blur-elysium-slide-content');

        if (!title) {
            if (existingEl) existingEl.remove();
            return;
        }

        if (existingEl) {
            if (existingEl.tagName.toLowerCase() === headlineElement) {
                existingEl.innerHTML = title;
                return;
            }

            const newEl = document.createElement(headlineElement);
            newEl.className = existingEl.className;
            newEl.setAttribute('data-elysium-slide-headline', this.slideId);
            newEl.innerHTML = title;
            existingEl.parentNode.replaceChild(newEl, existingEl);
        } else if (contentEl) {
            const el = document.createElement(headlineElement);
            el.className = 'blur-elysium-slide-headline';
            el.setAttribute('data-elysium-slide-headline', this.slideId);
            el.innerHTML = title;
            contentEl.insertBefore(el, contentEl.firstChild);
        }
    }

    updateButton(slide) {
        const linking = slide.slideSettings && slide.slideSettings.slide && slide.slideSettings.slide.linking;
        const overlay = linking && linking.overlay === true;
        const url = slide.url;
        const buttonLabel = slide.buttonLabel;
        const slideEl = this.el;
        if (!slideEl) return;

        const existingOverlay = slideEl.querySelector('[data-elysium-slide-link-overlay]');
        const existingActions = slideEl.querySelector('.blur-elysium-slide-actions');

        if (overlay && url) {
            if (existingActions) existingActions.remove();

            if (!existingOverlay) {
                const overlayEl = document.createElement('a');
                overlayEl.className = 'blur-elysium-slide-link-overlay';
                overlayEl.setAttribute('data-elysium-slide-link-overlay', this.slideId);
                overlayEl.setAttribute('href', url);

                const headlineEl = document.querySelector('[data-elysium-slide-headline]');
                overlayEl.setAttribute('aria-label', headlineEl ? headlineEl.textContent || '' : '');

                if (linking.openExternal) {
                    overlayEl.setAttribute('target', '_blank');
                    overlayEl.setAttribute('rel', 'noopener');
                }

                const container = slideEl.querySelector('[data-elysium-slide-container]');
                if (container && container.nextSibling) {
                    slideEl.insertBefore(overlayEl, container.nextSibling);
                } else if (container) {
                    slideEl.appendChild(overlayEl);
                } else {
                    slideEl.appendChild(overlayEl);
                }
            } else {
                existingOverlay.setAttribute('href', url);
                if (linking.openExternal) {
                    existingOverlay.setAttribute('target', '_blank');
                    existingOverlay.setAttribute('rel', 'noopener');
                } else {
                    existingOverlay.removeAttribute('target');
                    existingOverlay.removeAttribute('rel');
                }
            }
        } else {
            if (existingOverlay) existingOverlay.remove();

            if (url && buttonLabel) {
                if (!existingActions) {
                    const actionsEl = document.createElement('div');
                    actionsEl.className = 'blur-elysium-slide-actions';

                    const btnEl = document.createElement('a');
                    btnEl.className = 'blur-elysium-slide-main-btn btn';
                    btnEl.setAttribute('data-elysium-slide-button', this.slideId);
                    btnEl.setAttribute('href', url);
                    btnEl.setAttribute('title', buttonLabel);
                    btnEl.textContent = buttonLabel;

                    const appearance = (linking && linking.buttonAppearance) || 'primary';
                    if (appearance) {
                        btnEl.classList.add(`btn-${appearance}`);
                    }

                    const size = (linking && linking.buttonSize) || null;
                    if (size && size !== 'md') {
                        btnEl.classList.add(`btn-${size}`);
                    }

                    if (linking && linking.openExternal) {
                        btnEl.setAttribute('target', '_blank');
                        btnEl.setAttribute('rel', 'noopener');
                    }

                    actionsEl.appendChild(btnEl);

                    const contentEl = slideEl.querySelector('.blur-elysium-slide-content');
                    if (contentEl) {
                        contentEl.appendChild(actionsEl);
                    }
                } else {
                    const btn = existingActions.querySelector('[data-elysium-slide-button]');
                    if (btn) {
                        btn.setAttribute('href', url);
                        btn.setAttribute('title', buttonLabel);
                        btn.textContent = buttonLabel;

                        const classes = Array.from(btn.classList);
                        classes.forEach((cls) => {
                            if (cls !== 'btn' && cls !== 'blur-elysium-slide-main-btn' && cls.indexOf('btn-') === 0) {
                                btn.classList.remove(cls);
                            }
                        });

                        const appearance = (linking && linking.buttonAppearance) || 'primary';
                        if (appearance) {
                            btn.classList.add(`btn-${appearance}`);
                        }

                        const size = (linking && linking.buttonSize) || null;
                        if (size && size !== 'md') {
                            btn.classList.add(`btn-${size}`);
                        }

                        if (linking && linking.openExternal) {
                            btn.setAttribute('target', '_blank');
                            btn.setAttribute('rel', 'noopener');
                        } else {
                            btn.removeAttribute('target');
                            btn.removeAttribute('rel');
                        }
                    }
                }
            } else {
                if (existingActions) existingActions.remove();
            }
        }
    }

    _removeExistingCoverMedia(element) {
        const existingPicture = element.querySelector('.blur-elysium-slide-cover-picture');
        const existingVideo = element.querySelector('.blur-elysium-slide-cover-video');
        if (existingPicture) existingPicture.remove();
        if (existingVideo) existingVideo.remove();
    }

    _createVideoElement(video) {
        const el = document.createElement('video');
        el.setAttribute('autoplay', '');
        el.setAttribute('muted', '');
        el.setAttribute('loop', '');
        el.className = 'blur-elysium-slide-cover-video';
        el.setAttribute('data-elysium-slide-cover-video', this.slideId);
        el.style.borderRadius = 'var(--slide-border-radius, 0px)';

        const source = document.createElement('source');
        source.setAttribute('src', video.url);
        source.setAttribute('type', video.mimeType || 'video/mp4');
        el.appendChild(source);

        return el;
    }

    _createPictureElement(covers) {
        const pictureEl = document.createElement('picture');
        pictureEl.className = 'blur-elysium-slide-cover-picture';

        covers.forEach((cover, index) => {
            if (index < covers.length - 1) {
                const sourceEl = document.createElement('source');
                sourceEl.setAttribute('srcset', cover.media.url);
                sourceEl.setAttribute('media', 'screen and (min-width:1px)');
                pictureEl.appendChild(sourceEl);
            }
        });

        const defaultCover = covers[covers.length - 1].media;
        const imgEl = document.createElement('img');
        imgEl.setAttribute('src', defaultCover.url);
        imgEl.setAttribute('width', '100%');
        imgEl.setAttribute('height', '100%');
        imgEl.className = 'blur-elysium-slide-cover-image';
        imgEl.setAttribute('data-elysium-slide-cover-image', this.slideId);
        imgEl.style.borderRadius = 'var(--slide-border-radius, 0px)';
        pictureEl.appendChild(imgEl);

        return pictureEl;
    }

    updateCoverMedia(slide, element) {
        this._removeExistingCoverMedia(element);

        const video = slide.slideCoverVideo;
        if (video && video.url) {
            element.insertBefore(this._createVideoElement(video), element.firstChild);
            return;
        }

        const covers = [];
        if (slide.slideCoverMobile && slide.slideCoverMobile.url) {
            covers.push({ viewport: 'mobile', media: slide.slideCoverMobile });
        }
        if (slide.slideCoverTablet && slide.slideCoverTablet.url) {
            covers.push({ viewport: 'tablet', media: slide.slideCoverTablet });
        }
        if (slide.slideCover && slide.slideCover.url) {
            covers.push({ viewport: 'desktop', media: slide.slideCover });
        }

        if (covers.length === 0) {
            return;
        }

        element.insertBefore(this._createPictureElement(covers), element.firstChild);
    }

    updateFocusImage(slide) {
        const wrapper = document.querySelector(`[data-elysium-slide-focus-image="${this.slideId}"]`);
        const media = slide.presentationMedia;

        if (media && media.url) {
            if (!wrapper) {
                const container = document.querySelector('[data-elysium-slide-container]');
                if (!container) {
                    return;
                }
                const newWrapper = document.createElement('div');
                newWrapper.className = 'blur-elysium-slide-image';
                newWrapper.setAttribute('data-elysium-slide-focus-image', this.slideId);
                container.appendChild(newWrapper);
                newWrapper.innerHTML = `<img src="${media.url}" class="d-block img-fluid" style="width: var(--slide-focus-image-w, 100%);">`;
            } else {
                wrapper.innerHTML = `<img src="${media.url}" class="d-block img-fluid" style="width: var(--slide-focus-image-w, 100%);">`;
            }
        } else if (wrapper) {
            wrapper.remove();
        }
    }

    updateCssClass(slide) {
        const element = this.el;
        if (!element) return;

        const cssClass = slide.slideSettings && slide.slideSettings.slide && slide.slideSettings.slide.cssClass;
        const newClasses = (cssClass || '').trim().split(/\s+/).filter((c) => c.length > 0);
        const previousClasses = this._appliedCssClasses || [];

        previousClasses.forEach((cls) => {
            element.classList.remove(cls);
        });

        newClasses.forEach((cls) => {
            element.classList.add(cls);
        });

        this._appliedCssClasses = newClasses;
    }

    updateSlide(data) {
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
        this.updateHeadline(slide);
        this.updateTextContent(slide);
        this.updateButton(slide);
        this.updateCoverMedia(slide, element);
        this.updateFocusImage(slide);
    }

    _handleMessage(event) {
        if (event.origin !== this.allowedOrigin) {
            return;
        }
        if (event.data && event.data.type === 'elysium-slide-update') {
            this.updateSlide(event.data);
        }
    }
}
