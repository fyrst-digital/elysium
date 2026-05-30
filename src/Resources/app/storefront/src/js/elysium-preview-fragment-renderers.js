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
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');

    function walk(node) {
        if (node.nodeType === Node.TEXT_NODE) {
            return node.textContent;
        }

        if (node.nodeType !== Node.ELEMENT_NODE) {
            return '';
        }

        const tagName = node.tagName.toLowerCase();

        if (allowed.has(tagName)) {
            const attrs = Array.from(node.attributes)
                .map((attr) => ` ${attr.name}="${attr.value.replace(/"/g, '&quot;')}"`)
                .join('');
            const children = Array.from(node.childNodes).map(walk).join('');
            return `<${tagName}${attrs}>${children}</${tagName}>`;
        }

        return Array.from(node.childNodes).map(walk).join('');
    }

    return Array.from(doc.body.childNodes).map(walk).join('');
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

function getMediaById(resolvedMedia, id) {
    if (!id || !resolvedMedia) return null;
    return resolvedMedia[id] || null;
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

function renderHeadline(slide, element, _resolvedMedia, _device) {
    const id = slide.id;
    const linking = slide.slideSettings?.slide?.linking || {};
    const headline = slide.slideSettings?.slide?.headline || {};
    const headlineElement = headline.element || 'div';

    let content;
    if (linking.type === 'product' && linking.showProductTitle && slide.product?.translated?.name) {
        content = slide.product.translated.name;
    } else if (linking.type === 'category' && linking.showCategoryTitle && slide.category?.translated?.name) {
        content = slide.category.translated.name;
    } else {
        content = slide.contentSettings?.title || '';
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

function renderDescription(slide, element, _resolvedMedia, _device) {
    const id = slide.id;
    const linking = slide.slideSettings?.slide?.linking || {};

    let content;
    if (linking.type === 'product' && linking.showProductDescription && slide.product?.translated?.description) {
        content = slide.product.translated.description;
    } else if (linking.type === 'category' && linking.showCategoryDescription && slide.category?.translated?.description) {
        content = slide.category.translated.description;
    } else {
        content = slide.contentSettings?.description || '';
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

function renderButton(slide, element, _resolvedMedia, _device) {
    const id = slide.id;
    const linking = slide.slideSettings?.slide?.linking || {};
    const url = slide.contentSettings?.url || '';
    const label = slide.contentSettings?.button?.label || '';
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

function renderCover(slide, element, resolvedMedia, device) {
    const id = slide.id;
    const borderRadius = slide.slideSettings?.slide?.borderRadius || 0;
    const style = `border-radius: var(--slide-border-radius, ${borderRadius}px);`;
    const contentCover = slide.contentSettings?.slideCover || {};

    const videoMedia = getMediaById(resolvedMedia, contentCover.videoId);

    const covers = {};
    const mobileMedia = getMediaById(resolvedMedia, contentCover.mobileId);
    const tabletMedia = getMediaById(resolvedMedia, contentCover.tabletId);
    const desktopMedia = getMediaById(resolvedMedia, contentCover.desktopId);

    if (mobileMedia) covers.mobile = mobileMedia;
    if (tabletMedia) covers.tablet = tabletMedia;
    if (desktopMedia) covers.desktop = desktopMedia;

    // No cover IDs in contentSettings — covers were removed or never set
    const hasAnyCoverId = Boolean(
        contentCover.mobileId || contentCover.tabletId ||
        contentCover.desktopId || contentCover.videoId
    );

    if (!hasAnyCoverId) {
        const existingPicture = element.querySelector('.blur-elysium-slide-cover-picture');
        const existingVideo = element.querySelector('.blur-elysium-slide-cover-video');
        if (existingPicture) existingPicture.remove();
        if (existingVideo) existingVideo.remove();
        return;
    }

    // Cover IDs exist but media not loaded yet — preserve server-rendered
    if (!videoMedia && Object.keys(covers).length === 0) {
        return;
    }

    // Now safe to remove existing elements
    const existingPicture = element.querySelector('.blur-elysium-slide-cover-picture');
    const existingVideo = element.querySelector('.blur-elysium-slide-cover-video');
    if (existingPicture) existingPicture.remove();
    if (existingVideo) existingVideo.remove();

    if (videoMedia) {
        const html = `<video autoplay muted loop class="blur-elysium-slide-cover-video" data-elysium-slide-cover-video="${id}" style="${style}"><source src="${videoMedia.url}" type="${videoMedia.mimeType}"></video>`;
        element.insertAdjacentHTML('afterbegin', html);
        return;
    }

    // Mobile-first fallback: select cover for the current device
    let selectedCover;
    switch (device) {
        case 'mobile':
            selectedCover = covers.mobile || null;
            break;
        case 'tablet':
            selectedCover = covers.tablet || covers.mobile || null;
            break;
        default: // desktop
            selectedCover = covers.desktop || covers.tablet || covers.mobile || null;
    }

    if (!selectedCover) {
        return;
    }

    const thumbs = selectedCover.metaData?._thumbnails || selectedCover.thumbnails || [];
    const srcset = createSrcset(thumbs);
    const imgHtml = `<img src="${selectedCover.url}" ${srcset ? `srcset="${srcset}"` : ''} class="blur-elysium-slide-cover-image" data-elysium-slide-cover-image="${id}" style="${style}" />`;
    const html = `<picture class="blur-elysium-slide-cover-picture">${imgHtml}</picture>`;
    element.insertAdjacentHTML('afterbegin', html);
}

function renderFocusImage(slide, element, resolvedMedia, _device) {
    const id = slide.id;
    const linking = slide.slideSettings?.slide?.linking || {};
    let imageMedia = getMediaById(resolvedMedia, slide.contentSettings?.focusImageId);

    if (linking.type === 'product' && slide.product?.cover?.media && linking.showProductFocusImage) {
        imageMedia = slide.product.cover.media;
    } else if (linking.type === 'category' && slide.category?.media && linking.showCategoryFocusImage) {
        imageMedia = slide.category.media;
    }

    const existing = element.querySelector('[data-elysium-slide-focus-image]');
    if (!imageMedia) {
        // Only remove existing element if there's definitely no focus image to show
        // (no ID in contentSettings and no product/category fallback)
        const hasFocusImageId = Boolean(slide.contentSettings?.focusImageId);
        const hasProductFallback = linking.type === 'product' && slide.product?.cover?.media && linking.showProductFocusImage;
        const hasCategoryFallback = linking.type === 'category' && slide.category?.media && linking.showCategoryFocusImage;

        if (!hasFocusImageId && !hasProductFallback && !hasCategoryFallback && existing) {
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
