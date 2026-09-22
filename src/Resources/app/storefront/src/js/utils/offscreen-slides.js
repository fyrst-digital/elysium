/**
 * Hide non-visible Swiper slides from assistive tech and the tab order.
 *
 * @param {ArrayLike<{ classList: { contains: (name: string) => boolean }, setAttribute: Function, removeAttribute: Function }>} slides
 */
export function syncOffscreenSlides(slides) {
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
