import { describe, it } from 'node:test'
import assert from 'node:assert/strict'
import { applyReducedMotion, pauseCoverVideos } from '../../src/Resources/app/storefront/src/js/utils/reduced-motion.js'

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
