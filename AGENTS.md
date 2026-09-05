# AGENTS.md - BlurElysiumSlider Plugin

Shopware 6.7 plugin (`shopware/core`, `storefront`, `elasticsearch` `^6.7`). Current version is in `composer.json`. Metadata lives there too; there is no `plugin.xml`.

## GitHub

Origin is `fyrst-digital/elysium`. For GitHub work, use this repository and the GitHub CLI (`gh`). If `gh` is not installed, say so.

## Cursor Cloud

Defined in `.cursor/` (`environment.json`, `Dockerfile`, `install.sh`, `start.sh`). Native setup (no `docker compose`): PHP 8.3, MariaDB, Composer, Node 20, `shopware-cli`. Shopware `v6.7.13.1` is cloned to `$HOME/shopware`; this repo is symlinked to `$HOME/shopware/custom/plugins/BlurElysiumSlider`. GitHub CI PHPUnit also runs against `v6.7.0.0` (see `.github/workflows/ci.yml`). CI lint is administration eslint + `npm run test:administration` only (no storefront lint).

```bash
# PHPUnit (test DB is bootstrapped by install.sh)
cd "$HOME/shopware" && APP_ENV=test ./vendor/bin/phpunit \
  --configuration custom/plugins/BlurElysiumSlider/phpunit.xml

# Re-install/repair the Shopware test schema
cd "$HOME/shopware" && FORCE_INSTALL=true APP_ENV=test ./vendor/bin/phpunit \
  --configuration custom/plugins/BlurElysiumSlider/phpunit.xml --testsuite migration

# Lint and admin unit tests (plugin repo root; deps already installed by install.sh)
npm run lint:administration
npm run lint:storefront
npm run test:administration

# Validate the extension
shopware-cli extension validate .
```

MariaDB DSN: `mysql://app:app@127.0.0.1:3306/shopware` (tests use `shopware_test`). `start.sh` starts MariaDB on each boot.

## Local commands

From the Shopware root. Plugin path: `custom/static-plugins/BlurElysiumSlider`. Shopware JS builds strip plugin `node_modules`; run `npm install` in the plugin before lint.

```bash
# Build
docker compose exec web bin/build-administration.sh
docker compose exec web bin/build-storefront.sh
docker compose exec web bin/console cache:clear
docker compose exec web bin/console bundle:dump

# Lint and admin tests (re-install deps after a JS build)
docker compose exec -w /var/www/html/custom/static-plugins/BlurElysiumSlider web npm install
docker compose exec -w /var/www/html/custom/static-plugins/BlurElysiumSlider web npm run lint:administration
docker compose exec -w /var/www/html/custom/static-plugins/BlurElysiumSlider web npm run lint:storefront
docker compose exec -w /var/www/html/custom/static-plugins/BlurElysiumSlider web npm run test:administration

# PHPUnit (phpunit.xml already sets APP_ENV=test)
docker compose exec web ./vendor/bin/phpunit --configuration="custom/static-plugins/BlurElysiumSlider/phpunit.xml"
```

## Testing

PHPUnit (`phpunit.xml`): suites `migration`, `subscriber`, `service`, `command`, `demodata`. Admin unit tests: `npm run test:administration` → `tests/Administration/`.

## Architecture

- **DAL:** `blur_elysium_slides` + translation `blur_elysium_slides_translation` in `src/Core/Content/ElysiumSlides/`. Slide copy and media IDs live in translated `contentSettings` JSON (hydrator: `ElysiumSlidesHydrator`). Do not reintroduce cover FKs or translated `title`/`url` columns.
- **Viewport system:** mobile → tablet → desktop. Mixin `blur-device-utilities`; method `viewportsPlaceholder()`. Also `blur-style-utilities`.
- **CMS:** elements `blur-elysium-slider` / `blur-elysium-banner` (resolvers `ElysiumSliderCmsElementResolver`, `ElysiumBannerCmsElementResolver`). Section `blur-elysium-section` is injected by overriding `sw-cms-section`, not `registerCmsSection`.
- **Events:** `ElysiumSlidesResultEvent`, `ElysiumSlidesCriteriaEvent`, `ElysiumCmsSlidesCriteriaEvent`. `ElysiumCmsSlidesResultEvent` is gone.
- **Lifecycle:** `src/Bootstrap/Lifecycle.php`. PostUpdate `Version210` only runs when upgrading from < 2.0.0.
- **Twig functions:** `camel_to_kebab_case`, `create_srcset`.
- **Storefront plugins:** `ElysiumSliderPlugin` (`[data-elysium-slider]`), `ElysiumSlidePreview` (`[data-elysium-slide-preview]`).
- **Preview:** `/elysium-preview/{elementType}/{slideId}`; schema source of truth is PHP (`elysium:preview-schema:generate`).
- **Store API:** `GET|POST /store-api/elysium-slide`, `GET /store-api/elysium-slide/{slideId}` (media IDs in `contentSettings`; consumers resolve media themselves).
- **Admin API:** `POST /api/_action/elysium-slides/{export,import,switch-cover-images}`.
- **ACL:** `blur_elysium_slides.{viewer,editor,creator,deleter,exporter,importer}`.
- **Feature flags** (`Defaults::FEATURES`): `elysium_preview_elasticsearch`, `elysium_preview_time_control`, `elysium_preview_import_export`.
- **CLI:** `elysium:demodata`, `elysium:slides:export`, `elysium:slides:import`, `elysium:slides:switch-cover-images`, `elysium:preview-schema:generate`.
- **Admin alias:** `@elysium/*` → `src/Resources/app/administration/src/*`.

## File structure

```
src/
├── Core/Content/ElysiumSlides/   # Entity, translation, events, sales channel, demodata
├── DataResolver/                  # CMS resolvers
├── Migration/                     # DB migrations
├── Bootstrap/                     # Lifecycle + PostUpdate/Version210
├── Command/                       # CLI
├── Service/                       # Import/export, validation, cache, cover switch
├── Subscriber/
├── Preview/                       # Live-preview schema (PHP source of truth)
├── Twig/
├── Struct/
├── Storefront/Controller/
├── Administration/Controller/
├── Elasticsearch/
├── MessageHandler/
└── Resources/
    ├── app/administration/src/    # Vue, mixins, Pinia, CMS
    ├── app/storefront/src/        # JS plugins, SCSS
    └── views/storefront/          # Twig
```

## Naming

| Type | Convention |
|------|------------|
| CMS types / section | `blur-elysium-*` |
| CMS Vue (element / block) | `cms-el-blur-elysium-*`, `sw-cms-block-blur-elysium-*` |
| Slide Builder UI | `elysium-*` |
| Form primitives | `py-*` |
| Entity | `blur_elysium_slides` |
| Pinia stores | `elysiumSlide`, `elysiumUI`, `elysiumCMS`, `elysiumMedia` |

Do not use `elyium-*`. Vue/PHP/CMS conventions live in `.agents/skills/`.

## Skills and subagents

- Skills (portable): `.agents/skills/` — `changelog`, `administration-vue`, `php-migrations`, `cms-storefront`
- Cursor subagents: `.cursor/agents/` — `changelog`, `plugin-reviewer`
