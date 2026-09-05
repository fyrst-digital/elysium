---
name: administration-vue
description: Administration Vue/TypeScript conventions for the Elysium Slide Builder — wrapComponentConfig, Pinia stores, device mixins, naming prefixes, and translated contentSettings. Use when editing administration components, mixins, stores, CMS config UI, or tests/Administration.
paths:
  - "src/Resources/app/administration/**"
  - "tests/Administration/**"
---

# Administration Vue

## Components

Use `Component.wrapComponentConfig`. Register in `src/Resources/app/administration/src/main.ts` via `useComponentRegister` (CMS elements also call `cmsService.registerCmsElement` in their `index.ts`). Alias `@elysium/*` → `src/Resources/app/administration/src/*`.

```typescript
import template from './template.html.twig';

const { Component, Mixin, Store } = Shopware;

export default Component.wrapComponentConfig({
    template,
    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('blur-device-utilities'),
        Mixin.getByName('blur-style-utilities'),
    ],
    computed: {
        elysiumSlide() {
            return Store.get('elysiumSlide');
        },
        elysiumUI() {
            return Store.get('elysiumUI');
        },
        device() {
            return this.elysiumUI.device;
        },
    },
    created() {
        this.viewportsSettings = this.elysiumSlide.slide?.slideSettings?.viewports;
    },
});
```

## Naming

| Prefix | Use |
|--------|-----|
| `elysium-*` | Slide Builder, settings, IAP |
| `py-*` | Form kit (`py-text-field`, `py-device-switch`, …) |
| `blur-elysium-*` | CMS types and section Vue |
| `cms-el-blur-elysium-*` | CMS element Vue |
| `sw-cms-block-blur-elysium-*` | CMS block Vue |

Do not invent `elyium-*`.

## Pinia

`Store.register` in `main.ts`. IDs: `elysiumSlide`, `elysiumUI`, `elysiumCMS`, `elysiumMedia`.

Media IDs live on translated `contentSettings` (`slideCover.mobileId|tabletId|desktopId|videoId`, `focusImageId`). Resolve via `elysiumMedia`, not removed cover associations.

## Viewports

Order: mobile → tablet → desktop. Inherit from smaller viewports via `viewportsPlaceholder(property, fallback)` on mixin `blur-device-utilities`. Set `this.viewportsSettings` to the viewport map in `created()`. Device lives on `elysiumUI.device`.

## Create-slide defaults

Creating a slide must not copy the previous slide’s content, linking, or media. See `tests/Administration/create-slide-defaults.spec.ts`.

## Lint

From plugin root (re-`npm install` after a Shopware JS build): `npm run lint:administration`. Admin unit tests: `npm run test:administration`.
