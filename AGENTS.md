# AGENTS.md - BlurElysiumSlider Plugin

Shopware 6.7 plugin. Version 4.6.x.

## Commands

```bash
# Build (from Shopware root)
symfony run bin/build-administration.sh
symfony run bin/build-storefront.sh
symfony console cache:clear
symfony console bundle:dump

# Lint (from plugin root)
npm run lint:administration
npm run lint:storefront

# Tests (from Shopware root)
./vendor/bin/phpunit --configuration="custom/static-plugins/BlurElysiumSlider/phpunit.xml"
```

## Testing

PHPUnit tests in `tests/Migration/`.

## Code Patterns

### Vue Component
```typescript
const { Component, Mixin, Store, Data } = Shopware;
const { Criteria } = Data;

export default Component.wrapComponentConfig({
    template,
    inject: ['repositoryFactory', 'acl'],
    mixins: [Mixin.getByName('placeholder')],
    props: { slideId: { type: String, required: false, default: null } },
    data() { return { isLoading: true }; },
    computed: { elysiumSlide() { return Store.get('elysiumSlide'); } },
    methods: { loadSlide(): void { /* ... */ } },
    created(): void { this.loadSlide(); },
});
```

### Pinia Store
```typescript
export default {
    id: 'elysiumSlide',
    state: (): SlideState => ({ slide: null }),
    getters: { slideViewportSettings(state: SlideState) { return state.slide?.slideSettings?.viewports ?? null; } },
    actions: { setSlide(slide: Entity<'blur_elysium_slides'>): void { this.slide = slide; } },
};
```

### PHP Migration
```php
public function update(Connection $connection): void {
    if ($this->isAlreadyMigrated($connection, $id)) return;
    $connection->transactional(function (Connection $connection) { /* ... */ });
}
```

## Architecture

- **Entity**: `blur_elysium_slides` in `src/Core/Content/ElysiumSlides/`
- **Viewport System**: mobile → tablet → desktop. Use `viewportsPlaceholder()` mixin.
- **Data Resolvers**: `ElysiumSliderCmsElementResolver`, `ElysiumBannerCmsElementResolver`
- **Events**: `ElysiumSlidesResultEvent`, `ElysiumSlidesCriteriaEvent`, `ElysiumCmsSlidesCriteriaEvent`
- **Lifecycle**: `src/Bootstrap/Lifecycle.php`, `src/Bootstrap/PostUpdate/Version{VERSION}/`
- **Twig**: `camel_to_kebab_case`, `create_srcset`

## File Structure
```
src/
├── Core/Content/ElysiumSlides/   # Entity + Events
├── DataResolver/                  # CMS resolvers
├── Migration/                     # DB migrations
├── Bootstrap/                     # Lifecycle + PostUpdate
├── Twig/                          # Extensions
├── Struct/                        # DTOs
├── Subscriber/                    # Event subscribers
└── Resources/
    ├── app/administration/src/   # Vue components, mixins, stores
    ├── app/storefront/src/       # JS plugins, SCSS
    └── views/storefront/         # Twig templates
```

## Naming

| Type | Convention |
|------|------------|
| CMS Elements | `blur-elysium-*` |
| UI Components | `elyium-*` |
| Entity | `blur_elysium_slides` |
| Pinia Stores | `elysium*` |

## Subagents

- `@changelog` - Changelog workflow
