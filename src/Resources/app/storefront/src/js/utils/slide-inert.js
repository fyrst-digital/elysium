/**
 * Hide non-visible Swiper slides from assistive tech and the tab order.
 *
 * @param {ArrayLike<{ classList: { contains: (name: string) => boolean }, setAttribute: Function, removeAttribute: Function }>} slides
 */
export function syncSlideInert(slides) {
    if (!slides?.length) {
        return
    }

    Array.from(slides).forEach((slide) => {
        if (slide.classList.contains('swiper-slide-visible')) {
            slide.removeAttribute('inert')
            slide.removeAttribute('aria-hidden')
            return
        }

        slide.setAttribute('inert', '')
        slide.setAttribute('aria-hidden', 'true')
    })
}

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
