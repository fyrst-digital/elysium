# AGENTS.md - BlurElysiumSlider Plugin

Coding agent instructions for the BlurElysiumSlider Shopware 6.7 plugin.

## Plugin Purpose

BlurElysiumSlider is a comprehensive Shopware 6.7 CMS extension that transforms Shopping Experience layouts with flexible sliders, banners, and a powerful slide builder. It enables merchants to create visually rich, responsive content without coding or design expertise.

## Plugin Version & Compatibility

Version 4 of `BlurElysiumSlider` is exclusively compatible with Shopware 6.7. It does not support earlier Shopware versions (<= 6.6).

## End User Features

### CMS Elements
- **Elysium Slider**: Multi-slide carousel with configurable autoplay, navigation arrows, pagination styles, slides-per-view, and spacing per device viewport
- **Elysium Banner**: Single-slide display for hero images, SEO-optimized content blocks, or product highlights
- **Elysium Section**: Flexible grid layout system for Shopping Experience pages—drag-and-drop column widths, merge rows, reorder content visually

### Slide Builder
- Create reusable slides with headline (H1-H6 tags), description, and call-to-action buttons
- Link slides to products (auto-adopts product image, name, description) or custom URLs
- Full-surface clickable slides or button-based linking
- Custom Twig templates for advanced slide designs
- Custom field sets integration for extended content

### Media & Visuals
- Responsive cover images per device (mobile, tablet, desktop)
- Video support for slide backgrounds
- Color and gradient backgrounds
- Focus point positioning for images
- Lighthouse-optimized: picture tags, lazy loading, thumbnails, fixed aspect ratios

### Responsive Design (Mobile-First)
- Viewport-specific settings cascade: mobile → tablet → desktop
- Inheritance system: `null` values automatically adopt from smaller viewport
- Device-specific aspect ratios instead of fixed dimensions
- Per-viewport slide count and spacing controls

### SEO & Accessibility
- Semantic HTML with configurable heading tags (H1-H6)
- ARIA labels and live regions for slider navigation
- Screen reader announcements on slide change

## Build, Lint & Test Commands
Within our project we only use bun. While on the shopware root level only npm is being used.

### Lint Commands
```bash
# Lint administration code (TypeScript/Vue)
bun run lint:administration

# Lint storefront code (JavaScript/Vue)
bun run lint:storefront

# Auto-fix lint issues
bun run lint:administration:fix
bun run lint:storefront:fix
```

### Build Commands (run from Shopware root)
```bash
# Build administration assets
symfony run bin/build-administration.sh

# Build storefront assets
symfony run bin/build-storefront.sh

# Clear cache after changes
symfony console cache:clear
symfony console bundle:dump
```

### Tests
No automated test framework is currently configured. Manual testing required.

## Code Style Guidelines

### TypeScript/JavaScript

#### Imports
- Use path alias `@elysium` for admin source files: `import foo from '@elysium/component/foo'`
- Import Shopware globals from window: `const { Component, Store, Data } = Shopware;`
- Group imports: external libs → Shopware globals → local modules → types

#### Formatting
- Indentation: Tabs (not spaces)
- No semicolons at end of statements (JavaScript)
- Semicolons required in TypeScript
- Single quotes for strings

#### TypeScript Rules (enforced by ESLint)
- `@typescript-eslint/no-explicit-any`: error - never use `any`
- `@typescript-eslint/no-unused-vars`: error
- `@typescript-eslint/no-non-null-assertion`: error
- Use explicit types for function parameters and return values

#### Vue Component Pattern
```typescript
const { Component, Mixin, Store, Data } = Shopware;
const { Criteria } = Data;

export default Component.wrapComponentConfig({
    template,
    
    inject: ['repositoryFactory', 'acl'],
    
    mixins: [
        Mixin.getByName('placeholder'),
    ],
    
    props: {
        slideId: {
            type: String,
            required: false,
            default: null,
        },
    },
    
    data() {
        return {
            isLoading: true,
        };
    },
    
    computed: {
        elysiumSlide() {
            return Store.get('elysiumSlide');
        },
    },
    
    methods: {
        loadSlide(): void {
            // implementation
        },
    },
    
    created(): void {
        this.loadSlide();
    },
});
```

#### Pinia Store Pattern
```typescript
interface SlideState {
    slide: Entity<'blur_elysium_slides'> | null;
}

export default {
    id: 'elysiumSlide',
    state: (): SlideState => ({
        slide: null,
    }),
    getters: {
        slideViewportSettings(state: SlideState) {
            return state.slide?.slideSettings?.viewports ?? null;
        },
    },
    actions: {
        setSlide(slide: Entity<'blur_elysium_slides'>): void {
            this.slide = slide;
        },
    },
};
```

### PHP

#### Formatting
- `declare(strict_types=1);` at top of every file
- PSR-4 autoloading: namespace `Blur\BlurElysiumSlider\`
- Class constants for entity names: `public const ENTITY_NAME = 'blur_elysium_slides';`

#### Type Declarations
```php
public function getEntityName(): string
{
    return self::ENTITY_NAME;
}

public function __construct(
    private readonly EntityRepository $elysiumSlidesRepository,
    private readonly EventDispatcherInterface $eventDispatcher,
) {}
```

#### Migration Pattern
```php
public function update(Connection $connection): void
{
    if ($this->isAlreadyMigrated($connection, $mediaFolderId)) {
        return;
    }
    
    $connection->transactional(function (Connection $connection) {
        // migration logic
    });
}
```

Use `Defaults::MIGRATION_COLUMN_EXISTS` and `Defaults::MIGRATION_COLUMN_NOT_EXISTS` constants for error matching.

### Twig Templates

#### Block Naming
- Prefix blocks with component/element name: `cms_element_elysium_slider`
- Use descriptive suffixes: `_header`, `_navigation`, `_pagination`

#### Variables
```twig
{% set cmsElementClasses = ['cms-element-elysium-slider'] %}
{% set showContainerWrapper = element.config.settings.value.containerWidth is same as('content') %}
```

#### Includes
```twig
{% sw_include '@Storefront/storefront/component/elysium-slider/header.html.twig' with {
    title: sliderTitle,
    titleTag: 'div'
} %}
```

### SCSS/CSS

#### CSS Variables Pattern
```scss
#elysiumSlider-{{ element.id }} {
    --slide-aspect-ratio: 16 / 9;
    --slide-max-height: 600px;
}
```

## Architecture Patterns

### Viewport-Responsive System (Mobile-First)
Settings cascade: mobile → tablet → desktop. `null` values inherit from previous viewport.

```typescript
slide.slideSettings.viewports = {
    mobile: { slide: {...}, container: {...} },
    tablet: { ... },  // nulls inherit from mobile
    desktop: { ... }  // nulls inherit from tablet
}
```

Use `viewportsPlaceholder()` from `blur-device-utilities` mixin to resolve inherited values.

### Component Registration
```typescript
// Lazy-loaded components
useComponentRegister([
    { name: 'blur-icon', path: () => import('@elysium/component/icon') },
]);

// Override Shopware components
useComponentOverride([
    { name: 'sw-cms-section', path: () => import('@elysium/extension/sw-cms-section') },
]);
```

### CMS Element Registration
```typescript
Service('cmsService').registerCmsElement({
    name: 'blur-elysium-slider',
    label: 'blurElysiumSlider.label',
    component: 'cms-el-blur-elysium-slider',
    configComponent: 'cms-el-blur-elysium-slider-config',
    defaultConfig: defaultSliderSettings,
});
```

### Entity Types
```typescript
interface CustomEntities extends EntitySchema.Entities {
    blur_elysium_slides: blur_elysium_slides;
}

type ElysiumSlidesCollection = EntityCollection<'blur_elysium_slides'>;
```

## Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| CMS Elements | `blur-elysium-*` | `blur-elysium-slider` |
| UI Components | `blur-*` (transitioning to `py-*`) | `blur-card-title` |
| Slide Components | `elysium-slide-*` | `elysium-slide-preview` |
| Pinia Stores | `elysium*` | `elysiumSlide` |
| Entity | snake_case | `blur_elysium_slides` |
| PHP Classes | PascalCase | `ElysiumSlidesDefinition` |
| TypeScript types | PascalCase | `SlideSettings` |
| TypeScript interfaces | PascalCase | `ViewportConfig` |

## File Organization

```
src/
├── Core/Content/ElysiumSlides/   # Entity definitions
├── DataResolver/                  # CMS data resolvers
├── Migration/                     # Database migrations
├── Bootstrap/                     # Lifecycle hooks
├── Resources/
│   ├── app/
│   │   ├── administration/src/
│   │   │   ├── component/         # Vue components
│   │   │   ├── composables/       # Vue composables
│   │   │   ├── mixin/             # Global mixins
│   │   │   ├── states/            # Pinia stores
│   │   │   └── types/             # TypeScript types
│   │   └── storefront/src/
│   │       └── js/                # Storefront JS plugins
│   └── views/storefront/          # Twig templates
```

## Error Handling

- Use try/catch with specific exception handling
- Log warnings with `console.warn()`, errors with `console.error()`
- PHP: Use `@internal` annotation for migration classes
- Dispatch events for extensibility in data resolvers

## Changelog

Create partial changelogs in `.changelogs/` for branches prefixed with `fix/` or `feat/`:
```markdown
---
title: Fixed pagination display
issue: #123
---
# Fix: Fixed pagination display
* Pagination bullets now display correctly with bar style
```

## Dependencies

- **swiper**: Storefront slider functionality
- **vite**: Administration build tool
- **pinia**: State management (replaced Vuex in v4.0)
- **@shopware-ag/meteor-admin-sdk**: Shopware admin SDK