---
applyTo: '**'
---
# BlurElysiumSlider Plugin - AI Coding Agent Instructions

## Plugin Overview
**BlurElysiumSlider** is a comprehensive Shopware 6.7 plugin that extends the CMS system with advanced slider, banner, and slide builder functionality. It provides viewport-responsive slide management with a custom entity system and rich administration interface.

**Key Features:**
- Custom `blur_elysium_slides` entity with translations and media associations
- CMS elements: `blur-elysium-slider`, `blur-elysium-banner`
- CMS section: `blur-elysium-section`
- Viewport-responsive settings (mobile/tablet/desktop) with mobile-first inheritance
- Vite-based administration build with TypeScript
- Swiper.js integration for storefront sliders

## Architecture

### Core Components

#### Custom Entity System
- **Entity Definition**: `ElysiumSlidesDefinition` in `src/Core/Content/ElysiumSlides/`
- **Entity Name**: `blur_elysium_slides`
- **Key Fields**:
  - Translations: `name`, `title`, `description`, `buttonLabel`, `url`, `customFields`, `contentSettings`
  - Media: `slideCover`, `slideCoverMobile`, `slideCoverTablet`, `slideCoverVideo`, `presentationMedia`
  - Product association: `product_id`
  - JSON field: `slideSettings` (viewport-responsive configuration)

#### CMS Integration
- **Data Resolvers** (`src/DataResolver/`):
  - `ElysiumSliderCmsElementResolver` - Fetches slides for slider elements
  - `ElysiumBannerCmsElementResolver` - Fetches single slide for banner elements
- **Events for Extensibility**:
  - `ElysiumCmsSlidesResultEvent` - Dispatched after slides are fetched
  - `ElysiumCmsSlidesCriteriaEvent` - Dispatched before fetching to modify criteria

### Viewport-Responsive System

**Mobile-First Inheritance Pattern:**
Settings cascade from mobile → tablet → desktop. If a value is `null` at tablet/desktop, it inherits from the previous viewport.

**Viewport Configuration Structure:**
```typescript
slide.slideSettings.viewports = {
  mobile: { slide: {...}, container: {...}, content: {...}, image: {...}, coverMedia: {...} },
  tablet: { ... },
  desktop: { ... }
}
```

**Device Utilities Mixin:**
- Location: `src/Resources/app/administration/src/mixin/device-utilities.mixin.ts`
- Key method: `viewportsPlaceholder(property, fallback, snippetPrefix, viewportsSettings)`
  - Automatically resolves inherited values across viewports
  - Used throughout UI to show effective values in placeholders

**Breakpoint Mapping:**
- Mobile: `xs` (Shopware breakpoint)
- Tablet: `md` 
- Desktop: `xl`
- Configurable via `config/breakpoints.xml`

## Development Workflows

### Administration Build
```bash
# From shopware root
symfony run bin/build-administration.sh
```

**Build Configuration:**
- Vite-based: `vite.config.mts`
- Path alias: `@elysium` → `src/Resources/app/administration/src/`
- TypeScript with strict rules (no-explicit-any, no-unused-vars enforced)

### Storefront Build
```bash
# From shopware root
symfony run bin/build-storefront.sh
```

**Build Configuration:**
- Webpack-based: `build/webpack.config.js`
- Swiper.js for slider functionality
- Custom SCSS in `src/scss/`

### Database Migrations
**Pattern:**
```php
class Migration1234567890Example extends MigrationStep {
    public function update(Connection $connection): void {
        try {
            $connection->executeStatement('ALTER TABLE ...');
        } catch (\Throwable $e) {
            // Use Defaults constants for error matching
            if (!preg_match(Defaults::MIGRATION_COLUMN_EXISTS, $e->getMessage())) {
                throw $e;
            }
        }
    }
}
```

**Constants Available:**
- `Defaults::MIGRATION_COLUMN_EXISTS`
- `Defaults::MIGRATION_COLUMN_NOT_EXISTS`

### Plugin Lifecycle Hooks
**Bootstrap/Lifecycle Pattern:**
```php
class BlurElysiumSlider extends Plugin {
    public function postInstall(InstallContext $context): void {
        $lifecycle = new Lifecycle($this->container);
        $lifecycle->postInstall($context);
    }
    
    public function postUpdate(UpdateContext $context): void {
        $lifecycle = new Lifecycle($this->container);
        $lifecycle->postUpdate($context); // Version-gated updates
    }
}
```

**Version-Specific Updates:**
Location: `src/Bootstrap/PostUpdate/Version{VERSION}/Updater.php`

## Administration Development

### State Management (Pinia)
**Registered Stores:**
```typescript
Store.register(slideStore);    // Slide data management
Store.register(uiStore);        // UI state (device view)
Store.register(cmsStore);       // CMS-specific state

// Access in components
const slideStore = Store.get('elysiumSlide');
const uiStore = Store.get('elysiumUI');
```

### Component Registration Pattern
```typescript
// Lazy-loaded with dynamic imports
useComponentRegister([
  { name: 'component-name', path: () => import('@elysium/path/to/component') }
]);

// Override existing Shopware components
useComponentOverride([
  { name: 'sw-cms-section', path: () => import('@elysium/extension/sw-cms-section') }
]);
```

### Custom Mixins

**blur-device-utilities** (`src/Resources/app/administration/src/mixin/device-utilities.mixin.ts`):
- `viewportsPlaceholder(property, fallback, snippetPrefix, viewportsSettings)` - Resolve inherited viewport values
- `deviceViewports()` - Get viewport list
- `currentViewportIndex` - Current viewport index for inheritance logic

**blur-style-utilities** (`src/Resources/app/administration/src/mixin/style-utilities.mixin.ts`):
- `viewStyle(styles)` - Responsive inline styles helper

### TypeScript Types
**Key Type Definitions:**
- `src/Resources/app/administration/src/types/slide.ts` - Slide entity and settings
- `src/Resources/app/administration/src/types/slider.ts` - Slider element config
- `src/Resources/app/administration/src/types/banner.ts` - Banner element config

**Custom Entity Type:**
```typescript
interface CustomEntities extends EntitySchema.Entities {
    blur_elysium_slides: blur_elysium_slides;
}

// Usage
type Entity<EntityName extends keyof CustomEntities>
type EntityCollection<EntityName extends keyof CustomEntities>
```

### CMS Element Structure

**Registration Pattern:**
```typescript
Service('cmsService').registerCmsElement({
    name: 'blur-elysium-slider',
    label: 'blurElysiumSlider.label',
    component: 'cms-el-blur-elysium-slider',        // Preview in layout editor
    configComponent: 'cms-el-blur-elysium-slider-config',  // Settings panel
    previewComponent: 'cms-el-blur-elysium-slider-preview', // Block palette preview
    defaultConfig: defaultSliderSettings,
});
```

**Config Tabs Pattern:**
Components in `src/Resources/app/administration/src/component/cms/elements/{element}/config/`:
- `index.ts` - Main config component with tab switching
- `settings/` - General slider settings
- `sizing/` - Aspect ratio, dimensions
- `navigation/` - Pagination settings
- `arrows/` - Arrow navigation settings

## Storefront Development

### Swiper Integration
**Location:** `src/Resources/app/storefront/src/js/elysium-slider.js`

**Plugin Pattern:**
```javascript
export default class ElysiumSlider extends PluginBaseClass {
    static options = {
        swiperSelector: '[data-elysium-slider-swiper]',
        // ...
    };
    
    init() {
        // Initialize Swiper
    }
    
    listeners() {
        this.swiper.on('slideChange', this.onSlideChange.bind(this));
        this.$emitter.publish('listeners', { swiper: this.swiper });
    }
}
```

**Event System:**
- `this.$emitter.publish('eventName', data)` - Publish events
- Available events: `onSlideInit`, `onSlideChange`, `onPaginationUpdate`

### Template Structure
**Main Template:** `src/Resources/views/storefront/element/cms-element-blur-elysium-slider.html.twig`

**Key Twig Blocks:**
- `cms_element_blur_elysium_slider_css_styles` - Inline CSS variables
- `cms_element_elysium_slider` - Main slider wrapper
- `cms_element_elysium_slider_swiper` - Swiper container
- `cms_element_elysium_slider_slides` - Individual slides
- `cms_element_elysium_slider_navigation` - Pagination
- `cms_element_elysium_slider_arrows` - Navigation arrows

**CSS Variables Pattern:**
```css
#elysiumSlider-{{ element.id }} {
    --slide-aspect-ratio: 16 / 9;
    --slide-max-height: 600px;
    --pagination-bullet-color: #000;
    /* Responsive overrides in @media queries */
}
```

### Custom Twig Extensions
**Available Functions:**
- `camel_to_kebab_case(string)` - Convert camelCase to kebab-case
- `create_srcset(media, sizes)` - Generate responsive srcset

## Naming Conventions

### Component Naming
- **CMS Elements**: `blur-elysium-*` (legacy) → transitioning to `elysium-*`
- **UI Components**: `blur-*` (current) → planned transition to `py-*`
- **Slide Components**: `elysium-slide-*`

### File Organization
```
src/Resources/app/administration/src/
├── component/
│   ├── cms/
│   │   ├── elements/    # CMS element components
│   │   ├── blocks/      # CMS block components
│   │   └── section/     # CMS section components
│   ├── slides/          # Slide management UI
│   │   ├── detail/      # Slide edit page
│   │   ├── overview/    # Slide listing
│   │   └── form/        # Form sections
│   ├── slide/           # Reusable slide components
│   │   ├── preview/     # Slide preview renderer
│   │   ├── search/      # Slide search/picker
│   │   └── selection/   # Slide selection list
│   └── form/            # Form input components
├── composables/         # Vue composables
├── extension/           # Shopware component extensions
├── mixin/               # Global mixins
├── module/              # Admin module definition
├── states/              # Pinia stores
└── types/               # TypeScript definitions
```

## Configuration & Settings

### Plugin Configuration
**Breakpoint Mapping:** `src/Resources/config/breakpoints.xml`
```xml
<config domain="BlurElysiumSlider">
    <card>
        <input-field type="text">
            <name>breakpoints.mobile</name>
            <defaultValue>xs</defaultValue>
        </input-field>
        <!-- tablet, desktop -->
    </card>
</config>
```

### Custom Fields Integration
**Register Entity for Custom Fields:**
```typescript
const CustomFieldDataProviderService = Service('customFieldDataProviderService');
CustomFieldDataProviderService.addEntityName('blur_elysium_slides');
```

**Usage in Slide Detail:**
```typescript
loadCustomFieldSets() {
    const criteria = new Criteria();
    criteria.addFilter(Criteria.equals('relations.entityName', 'blur_elysium_slides'));
    // Fetch and render custom field sets
}
```

## Testing & Debugging

### Administration Debugging
- Vue DevTools for component inspection
- Pinia store state via `Store.get('storeName')`
- Browser console for type checking (TypeScript compiled)

### Storefront Debugging
- Browser DevTools for Swiper instance: `window.swiperInstance`
- Event debugging: Listen to `this.$emitter` events
- CSS variable inspection in DevTools

### Common Issues

1. **Viewport Settings Not Inheriting:**
   - Check `viewportsPlaceholder()` is used correctly
   - Verify `viewportsSettings` is set in component's `created()` hook
   - Ensure `null` values trigger inheritance (not `undefined`)

2. **CMS Element Not Showing:**
   - Run `symfony console bundle:dump` after changes
   - Clear cache: `symfony console cache:clear`
   - Rebuild administration: `symfony run ./bin/build-administration.sh`

3. **Slider Not Initializing:**
   - Check Swiper selector matches template
   - Verify `data-elysium-slider-swiper` attribute exists
   - Check browser console for JS errors

4. **Migration Errors:**
   - Use try/catch with `Defaults::MIGRATION_COLUMN_EXISTS` pattern
   - Test migrations against clean and existing databases
   - Version-gate breaking changes in `Bootstrap/PostUpdate/`

## External Dependencies
- **@iconify/utils** - Icon generation (not actively used)
- **swiper** - Slider library (storefront)
- **vite** - Administration build tool
- **@shopware-ag/meteor-admin-sdk** - Shopware admin SDK
- **pinia** - State management (replaced Vuex in v4.0)

## Documentation & Store
- Bilingual changelogs: `CHANGELOG.md`, `CHANGELOG_de-DE.md`
- Store listings: `store/de/`, `store/en/`
- Upgrade guides: `UPGRADE-4-0.md` (major version changes)

## Version History
- **Current:** 4.3.x - Swiper replacement, improved A11y
- **4.0.0:** Vite migration, Pinia (breaking changes - see UPGRADE-4-0.md)
- **2.x:** Legacy version with Vuex

## Important Notes
- Always use typed repositories: `EntityRepository<ElysiumSlidesCollection>`
- Follow mobile-first approach for viewport settings
- Dispatch extensibility events in data resolvers for third-party plugins
- Maintain backwards compatibility in templates (provide deprecated blocks)
- Keep TypeScript strict mode enabled (`@typescript-eslint/no-explicit-any`)
