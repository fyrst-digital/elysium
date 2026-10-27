import { describe, it } from 'node:test'
import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'
import { resolve } from 'node:path'

const coverTwig = readFileSync(
    resolve('src/Resources/views/storefront/component/blur-elysium-slide/includes/cover.html.twig'),
    'utf8',
)
const createSrcsetPhp = readFileSync(resolve('src/Twig/CreateSrcset.php'), 'utf8')
const previewRenderers = readFileSync(
    resolve('src/Resources/app/storefront/src/js/elysium-preview-fragment-renderers.js'),
    'utf8',
)

describe('storefront cover picture markup', () => {
    it('encodes srcset URLs so spaces in file names cannot drop <source> candidates', () => {
        assert.match(createSrcsetPhp, /function encodeSrcsetUrl/)
        assert.match(createSrcsetPhp, /%20/)
        assert.match(coverTwig, /srcset_url\(/)
        assert.match(coverTwig, /create_srcset\(coverThumbnails, /)
        assert.match(previewRenderers, /function encodeSrcsetUrl/)
        assert.match(previewRenderers, /%20/)
    })

    it('declares sizes on picture sources and the fallback image when srcset is used', () => {
        assert.match(coverTwig, /sizes="100vw"/)
        assert.equal((coverTwig.match(/sizes="100vw"/g) || []).length >= 3, true)
        assert.match(previewRenderers, /sizes="100vw"/)
    })

    it('uses the smallest available cover as the mobile-first default image', () => {
        assert.match(
            coverTwig,
            /defaultCover = slideCoverMobile\|default\(slideCoverTablet\|default\(slideCoverDesktop\)\)/,
        )
        assert.match(coverTwig, /slideCoverDesktop\.id != defaultCover\.id/)
        assert.match(coverTwig, /slideCoverTablet\.id != defaultCover\.id/)
        assert.doesNotMatch(coverTwig, /R0lGODlhAQABAIAAAAAAAP/)
    })

    it('emits desktop sources before tablet sources so the more specific min-width wins', () => {
        const desktopIndex = coverTwig.indexOf('blurElysiumBreakpoints.desktop')
        const tabletIndex = coverTwig.lastIndexOf('blurElysiumBreakpoints.tablet')
        assert.ok(desktopIndex > 0)
        assert.ok(tabletIndex > desktopIndex)
    })
})
