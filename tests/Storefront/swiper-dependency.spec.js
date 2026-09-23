import { describe, it } from 'node:test'
import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'
import { resolve } from 'node:path'

const storefrontPackage = JSON.parse(
    readFileSync(resolve('src/Resources/app/storefront/package.json'), 'utf8'),
)
const storefrontLock = JSON.parse(
    readFileSync(resolve('src/Resources/app/storefront/package-lock.json'), 'utf8'),
)
const sliderPlugin = readFileSync(
    resolve('src/Resources/app/storefront/src/js/elysium-slider.js'),
    'utf8',
)
const swiperVersion = storefrontPackage.dependencies.swiper

describe('swiper storefront dependency', () => {
    it('pins an exact Swiper 14 version in the storefront package', () => {
        assert.match(swiperVersion, /^14\.\d+\.\d+$/)
    })

    it('locks the same Swiper version that package.json pins', () => {
        assert.equal(storefrontLock.packages['node_modules/swiper'].version, swiperVersion)
    })

    for (const file of ['swiper-bundle.min.css', 'swiper.min.css']) {
        it(`ships ${file} from the pinned Swiper version`, () => {
            const css = readFileSync(resolve('src/Resources/public/css', file), 'utf8')

            assert.ok(
                css.startsWith(`/**\n * Swiper ${swiperVersion}\n`),
                `${file} header does not match Swiper ${swiperVersion}; run npm run copy:swiper-css`,
            )
        })
    }

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
