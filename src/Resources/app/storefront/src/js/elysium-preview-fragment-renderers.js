/**
 * Fragment renderers for the Elysium slide preview.
 *
 * Each renderer knows how to update the DOM for a specific fragment type.
 */

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

/**
 * Partial HTML Renderer
 * Fetches HTML from the backend and replaces DOM elements.
 */
function createPartialRenderer() {
    return {
        async render(fragment, data, element, plugin) {
            const html = await plugin._fetchPartial(fragment.name, data.slide);

            if (fragment.insertStrategy === 'replace-content') {
                // For cover: remove existing picture/video, insert new HTML at beginning
                const existingPicture = element.querySelector('.blur-elysium-slide-cover-picture');
                const existingVideo = element.querySelector('.blur-elysium-slide-cover-video');
                if (existingPicture) existingPicture.remove();
                if (existingVideo) existingVideo.remove();
                if (html.trim()) {
                    element.insertAdjacentHTML('afterbegin', html);
                }
                return;
            }

            const existingEl = document.querySelector(fragment.domSelector);
            if (existingEl) {
                if (html.trim()) {
                    existingEl.outerHTML = html;
                } else {
                    existingEl.remove();
                }
            } else if (html.trim() && fragment.fallbackContainer) {
                const container = document.querySelector(fragment.fallbackContainer);
                if (container) {
                    container.insertAdjacentHTML(fragment.fallbackPosition, html);
                }
            }
        },
    };
}

/**
 * Registry mapping fragment modes to renderer factories.
 */
export function createFragmentRenderers() {
    const renderers = {
        'css-variable': createCssVariableRenderer(),
        'preview-sizing': createPreviewSizingRenderer(),
        'partial': createPartialRenderer(),
    };

    return {
        get(mode) {
            return renderers[mode] || null;
        },
    };
}
