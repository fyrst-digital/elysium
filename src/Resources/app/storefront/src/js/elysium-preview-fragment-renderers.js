/**
 * Fragment renderers for the Elysium slide preview.
 *
 * Each renderer knows how to update the DOM for a specific fragment type.
 */

function stripTags(html, allowedTags = []) {
    if (!html) {
        return '';
    }
    const allowed = new Set(allowedTags.map((t) => t.replace(/[<>]/g, '').toLowerCase()));
    return html.replace(/<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, (match, tag) => {
        return allowed.has(tag.toLowerCase()) ? match : '';
    });
}

function createSrcset(thumbnails) {
    if (!Array.isArray(thumbnails)) {
        return '';
    }
    return thumbnails
        .filter((t) => t.url && t.width)
        .map((t) => `${t.url} ${t.width}w`)
        .join(', ');
}

/**
 * CSS Variable Renderer
 * Applies inline styles based on style mappings.
 */
function createCssVariableRenderer() {
    return {
        render(fragment, data, element, plugin) {
            plugin._clearManagedStyles(element);
            plugin.updateBaseStyles(data.slide, element);
            plugin.updateViewportStyles(data.slide, data.device || 'desktop', element);
            plugin.updateCssClass(data.slide);
        },
    };
}

/**
 * Preview Sizing Renderer
 * Updates aspect-ratio and max-width simulation.
 */
function createPreviewSizingRenderer() {
    return {
        render(fragment, data, element, plugin) {
            plugin._updatePreviewSizing(data, element);
        },
    };
}

function renderHeadline(slide, element) {
    const id = slide.id;
    const linking = slide.slideSettings?.slide?.linking || {};
    const headline = slide.slideSettings?.slide?.headline || {};
    const headlineElement = headline.element || 'div';

    let content;
    if (linking.type === 'product' && linking.showProductTitle && slide.product?.translated?.name) {
        content = slide.product.translated.name;
    } else {
        content = slide.title || '';
    }

    const whitelist = ['br', 'i', 'b', 'u', 'strong', 'span'];
    content = stripTags(content, whitelist);

    const existing = element.querySelector('[data-elysium-slide-headline]');
    if (!content) {
        if (existing) {
            existing.remove();
        }
        return;
    }

    const html = `<${headlineElement} class="blur-elysium-slide-headline" data-elysium-slide-headline="${id}">${content}</${headlineElement}>`;

    if (existing) {
        existing.outerHTML = html;
    } else {
        const container = element.querySelector('.blur-elysium-slide-content');
        if (container) {
            container.insertAdjacentHTML('afterbegin', html);
        }
    }
}

function renderDescription(slide, element) {
    const id = slide.id;
    const linking = slide.slideSettings?.slide?.linking || {};

    let content;
    if (linking.type === 'product' && linking.showProductDescription && slide.product?.translated?.description) {
        content = slide.product.translated.description;
    } else {
        content = slide.description || '';
    }

    const whitelist = ['p', 'br', 'span', 'i', 'b', 'u', 'strong', 'em', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'div', 'img', 'table', 'tr', 'td', 'th', 'tbody', 'thead'];
    content = stripTags(content, whitelist);

    const existing = element.querySelector('[data-elysium-slide-description]');
    if (!content) {
        if (existing) {
            existing.remove();
        }
        return;
    }

    const html = `<div class="blur-elysium-slide-description" data-elysium-slide-description="${id}">${content}</div>`;

    if (existing) {
        existing.outerHTML = html;
    } else {
        const container = element.querySelector('.blur-elysium-slide-content');
        if (container) {
            container.insertAdjacentHTML('beforeend', html);
        }
    }
}

function renderButton(slide, element) {
    const id = slide.id;
    const linking = slide.slideSettings?.slide?.linking || {};
    const url = slide.url || '';
    const label = slide.buttonLabel || '';
    const showActions = url && linking.overlay !== true && label;

    const existing = element.querySelector('.blur-elysium-slide-actions');
    if (!showActions) {
        if (existing) {
            existing.remove();
        }
        return;
    }

    const classes = ['blur-elysium-slide-main-btn', 'btn'];
    if (linking.buttonAppearance) {
        classes.push(`btn-${linking.buttonAppearance}`);
    }
    if (linking.buttonSize && linking.buttonSize !== 'md') {
        classes.push(`btn-${linking.buttonSize}`);
    }

    const isPreview = element.hasAttribute('data-elysium-slide-preview');
    const href = isPreview ? '#' : url;
    let attrs = '';
    if (isPreview) {
        attrs = ' onclick="return false"';
    }
    if (linking.openExternal === true) {
        attrs += ' target="_blank" rel="noopener"';
    }

    const html = `<div class="blur-elysium-slide-actions"><a href="${href}" title="${label}" class="${classes.join(' ')}" data-elysium-slide-button="${id}"${attrs}>${label}</a></div>`;

    if (existing) {
        existing.outerHTML = html;
    } else {
        const container = element.querySelector('.blur-elysium-slide-content');
        if (container) {
            container.insertAdjacentHTML('beforeend', html);
        }
    }
}

function renderCover(slide, element) {
    const id = slide.id;
    const borderRadius = slide.slideSettings?.slide?.borderRadius || 0;
    const style = `border-radius: var(--slide-border-radius, ${borderRadius}px);`;

    const existingPicture = element.querySelector('.blur-elysium-slide-cover-picture');
    const existingVideo = element.querySelector('.blur-elysium-slide-cover-video');
    if (existingPicture) {
        existingPicture.remove();
    }
    if (existingVideo) {
        existingVideo.remove();
    }

    if (slide.slideCoverVideo) {
        const html = `<video autoplay muted loop class="blur-elysium-slide-cover-video" data-elysium-slide-cover-video="${id}" style="${style}"><source src="${slide.slideCoverVideo.url}" type="${slide.slideCoverVideo.mimeType}"></video>`;
        element.insertAdjacentHTML('afterbegin', html);
        return;
    }

    const covers = {};
    if (slide.slideCoverMobile) {
        covers.mobile = slide.slideCoverMobile;
    }
    if (slide.slideCoverTablet) {
        covers.tablet = slide.slideCoverTablet;
    }
    if (slide.slideCover) {
        covers.desktop = slide.slideCover;
    }

    if (Object.keys(covers).length === 0) {
        return;
    }

    let sources = '';
    const breakpoints = { desktop: 1200, tablet: 768 };

    if (covers.desktop) {
        const thumbs = covers.desktop.metaData?._thumbnails || covers.desktop.thumbnails || [];
        const srcset = createSrcset(thumbs);
        sources += `<source ${srcset ? `srcset="${srcset}"` : `srcset="${covers.desktop.url}"`} media="screen and (min-width:${breakpoints.desktop}px)" />`;
    }

    if (covers.tablet) {
        const thumbs = covers.tablet.metaData?._thumbnails || covers.tablet.thumbnails || [];
        const srcset = createSrcset(thumbs);
        sources += `<source ${srcset ? `srcset="${srcset}"` : `srcset="${covers.tablet.url}"`} media="screen and (min-width:${breakpoints.tablet}px)" />`;
    }

    let imgHtml;
    if (covers.mobile) {
        const thumbs = covers.mobile.metaData?._thumbnails || covers.mobile.thumbnails || [];
        const srcset = createSrcset(thumbs);
        imgHtml = `<img src="${covers.mobile.url}" ${srcset ? `srcset="${srcset}"` : ''} class="blur-elysium-slide-cover-image" data-elysium-slide-cover-image="${id}" style="${style}" />`;
    } else {
        imgHtml = `<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="blur-elysium-slide-cover-image" data-elysium-slide-cover-image="${id}" style="${style}" />`;
    }

    const html = `<picture class="blur-elysium-slide-cover-picture">${sources}${imgHtml}</picture>`;
    element.insertAdjacentHTML('afterbegin', html);
}

function renderFocusImage(slide, element) {
    const id = slide.id;
    const linking = slide.slideSettings?.slide?.linking || {};
    let imageMedia = slide.presentationMedia;

    if (linking.type === 'product' && slide.product?.cover?.media && linking.showProductFocusImage) {
        imageMedia = slide.product.cover.media;
    }

    const existing = element.querySelector('[data-elysium-slide-focus-image]');
    if (!imageMedia) {
        if (existing) {
            existing.remove();
        }
        return;
    }

    const html = `<div class="blur-elysium-slide-image" data-elysium-slide-focus-image="${id}"><img src="${imageMedia.url}" class="d-block img-fluid" style="width: var(--slide-focus-image-w, 100%);" loading="eager" /></div>`;

    if (existing) {
        existing.outerHTML = html;
    } else {
        const container = element.querySelector('[data-elysium-slide-container]');
        if (container) {
            container.insertAdjacentHTML('beforeend', html);
        }
    }
}

/**
 * Client-side partial renderers keyed by fragment name.
 */
function createClientPartialRenderers() {
    return {
        headline: renderHeadline,
        description: renderDescription,
        button: renderButton,
        cover: renderCover,
        'focus-image': renderFocusImage,
    };
}

/**
 * Registry mapping fragment modes to renderer factories.
 */
export function createFragmentRenderers() {
    const clientRenderers = createClientPartialRenderers();

    const renderers = {
        'css-variable': createCssVariableRenderer(),
        'preview-sizing': createPreviewSizingRenderer(),
    };

    return {
        get(mode) {
            return renderers[mode] || null;
        },
        getClientRenderer(name) {
            return clientRenderers[name] || null;
        },
    };
}
