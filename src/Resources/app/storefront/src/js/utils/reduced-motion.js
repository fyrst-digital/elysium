/**
 * @param {object} options
 * @param {boolean} prefersReducedMotion
 * @returns {object}
 */
export function applyReducedMotion(options, prefersReducedMotion) {
    if (!prefersReducedMotion) {
        return options
    }

    return {
        ...options,
        autoplay: false,
    }
}

/**
 * @param {ParentNode} root
 */
export function pauseCoverVideos(root) {
    if (!root?.querySelectorAll) {
        return
    }

    root.querySelectorAll('[data-elysium-slide-cover-video]').forEach((video) => {
        video.pause?.()
        video.autoplay = false
        video.removeAttribute('autoplay')
    })
}
