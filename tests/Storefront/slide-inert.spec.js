import { describe, it } from 'node:test'
import assert from 'node:assert/strict'
import { applyReducedMotion, pauseCoverVideos, syncSlideInert } from '../../src/Resources/app/storefront/src/js/utils/slide-inert.js'

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

describe('syncSlideInert', () => {
    it('marks non-visible slides inert and hidden', () => {
        const visible = createSlide(true)
        const hidden = createSlide(false)

        syncSlideInert([visible, hidden])

        assert.equal(visible.attrs.inert, undefined)
        assert.equal(visible.attrs['aria-hidden'], undefined)
        assert.equal(hidden.attrs.inert, '')
        assert.equal(hidden.attrs['aria-hidden'], 'true')
    })
})

describe('applyReducedMotion', () => {
    it('disables autoplay when reduced motion is preferred', () => {
        const options = applyReducedMotion({ autoplay: { delay: 5000 }, speed: 300 }, true)

        assert.equal(options.autoplay, false)
        assert.equal(options.speed, 300)
    })

    it('keeps autoplay when reduced motion is not preferred', () => {
        const autoplay = { delay: 5000 }
        const options = applyReducedMotion({ autoplay }, false)

        assert.equal(options.autoplay, autoplay)
    })
})

describe('pauseCoverVideos', () => {
    it('pauses matching videos and clears autoplay', () => {
        const video = {
            pause() {
                this.paused = true
            },
            autoplay: true,
            removeAttribute(name) {
                this.removed = name
            },
        }
        const root = {
            querySelectorAll: (selector) => {
                assert.equal(selector, '[data-elysium-slide-cover-video]')
                return [video]
            },
        }

        pauseCoverVideos(root)

        assert.equal(video.paused, true)
        assert.equal(video.autoplay, false)
        assert.equal(video.removed, 'autoplay')
    })
})
