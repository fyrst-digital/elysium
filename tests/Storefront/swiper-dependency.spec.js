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

describe('swiper storefront dependency', () => {
    it('pins Swiper 14 in the storefront package', () => {
        assert.match(storefrontPackage.dependencies.swiper, /^\^14\./)
    })

    it('ships matching Swiper 14 bundle CSS', () => {
        assert.match(bundleCss, /^\/\*\*\n \* Swiper 14\./)
    })
})
