import { describe, it } from 'node:test'
import assert from 'node:assert/strict'
import { syncOffscreenSlides } from '../../src/Resources/app/storefront/src/js/utils/offscreen-slides.js'

function createSlide(visible) {
    const attrs = {}

    return {
        classList: {
            contains: (name) => name === 'swiper-slide-visible' && visible,
        },
        setAttribute: (name, value) => {
            attrs[name] = value
        },
        removeAttribute: (name) => {
            delete attrs[name]
        },
        attrs,
    }
}

describe('syncOffscreenSlides', () => {
    it('marks non-visible slides inert and hidden', () => {
        const visible = createSlide(true)
        const hidden = createSlide(false)

        syncOffscreenSlides([visible, hidden])

        assert.equal(visible.attrs.inert, undefined)
        assert.equal(visible.attrs['aria-hidden'], undefined)
        assert.equal(hidden.attrs.inert, '')
        assert.equal(hidden.attrs['aria-hidden'], 'true')
    })
})
