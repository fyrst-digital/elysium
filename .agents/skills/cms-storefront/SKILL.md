---
name: cms-storefront
description: CMS elements, blocks, Elysium section, resolvers, Twig, and storefront JS/SCSS for BlurElysiumSlider. Use when editing CMS config, DataResolver, storefront templates, slider plugins, live preview, or slide cover rendering.
paths:
  - "src/DataResolver/**"
  - "src/Resources/app/storefront/**"
  - "src/Resources/views/storefront/**"
  - "src/Storefront/**"
  - "src/Preview/**"
  - "src/Resources/app/administration/src/component/cms/**"
  - "src/Resources/app/administration/src/extension/**"
---

# CMS and storefront

## CMS types

| Kind | Name | How it is registered |
|------|------|---------------------|
| Element | `blur-elysium-slider` | `cmsService.registerCmsElement` + `ElysiumSliderCmsElementResolver` |
| Element | `blur-elysium-banner` | `cmsService.registerCmsElement` + `ElysiumBannerCmsElementResolver` |
| Block | `blur-elysium-slider`, `blur-elysium-banner` | `cmsService.registerCmsBlock`, category `blur-elysium-blocks` |
| Section | `blur-elysium-section` | Override `sw-cms-section` and `sw-cms-stage-section-selection`. **Not** `registerCmsSection`. |

No CMS `config.xml`. Element defaults: `settings.ts` next to the element.

Do not register leftover `cms-block-blur-elysium-block-two-col.html.twig` as a block.

Events: resolvers dispatch `ElysiumCmsSlidesCriteriaEvent`. `ElysiumCmsSlidesResultEvent` was removed.

## Twig

Under `src/Resources/views/storefront/`:

- `element/cms-element-blur-elysium-{slider,banner}.html.twig`
- `block/cms-block-blur-elysium-{slider,banner}.html.twig`
- `section/cms-section-blur-elysium-section.html.twig`
- Slide markup: `component/blur-elysium-slide/`

Functions (not filters): `camel_to_kebab_case`, `create_srcset`. Custom slide templates override the slide component Twig.

## Storefront JS / SCSS

`src/Resources/app/storefront/src/main.js`:

- `ElysiumSliderPlugin` → `[data-elysium-slider]`
- `ElysiumSlidePreview` → `[data-elysium-slide-preview]`

SCSS entry: `scss/base.scss` (slide, slider, banner, section, block partials).

## Live preview

Route: `GET /elysium-preview/{elementType}/{slideId}`. PHP schema in `src/Preview/` is the source of truth. After changing it, run `bin/console elysium:preview-schema:generate`.

## Lint

From plugin root: `npm run lint:storefront`. CI currently lints administration only.
