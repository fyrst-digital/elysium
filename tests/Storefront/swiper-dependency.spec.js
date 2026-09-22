import { describe, it } from 'node:test'
import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'
import { resolve } from 'node:path'

const storefrontPackage = JSON.parse(
    readFileSync(resolve('src/Resources/app/storefront/package.json'), 'utf8'),
)
const bundleCss = readFileSync(
    resolve('src/Resources/public/css/swiper-bundle.min.css'),
    'utf8',
)
const sliderPlugin = readFileSync(
    resolve('src/Resources/app/storefront/src/js/elysium-slider.js'),
    'utf8',
)

describe('swiper storefront dependency', () => {
    it('pins Swiper 14 in the storefront package', () => {
        assert.match(storefrontPackage.dependencies.swiper, /^\^14\./)
    })

    it('ships matching Swiper 14 bundle CSS', () => {
        assert.match(bundleCss, /^\/\*\*\n \* Swiper 14\./)
    })

    it('does not declare a swiper class field that Shopware would reset after init()', () => {
        assert.doesNotMatch(sliderPlugin, /^\s*swiper\s*=/m)
        assert.match(sliderPlugin, /this\.swiper\s*=\s*new Swiper/)
    })

    it('documents that a swiper class field is wiped after PluginBaseClass.init()', () => {
        class PluginBaseClass {
            constructor() {
                this.init()
            }

            init() {}
        }

        class BrokenSlider extends PluginBaseClass {
            swiper = null

            init() {
                this.swiper = { version: '14.2.0' }
            }
        }

        class FixedSlider extends PluginBaseClass {
            init() {
                this.swiper = { version: '14.2.0' }
            }
        }

        assert.equal(new BrokenSlider().swiper, null)
        assert.equal(new FixedSlider().swiper.version, '14.2.0')
    })
})
