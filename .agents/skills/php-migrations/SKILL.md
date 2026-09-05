---
name: php-migrations
description: PHP, DAL, migrations, PHPUnit, lifecycle, and feature-flag conventions for BlurElysiumSlider. Use when editing PHP, adding a database migration, writing PHPUnit tests, changing contentSettings, or working with plugin lifecycle.
paths:
  - "src/**/*.php"
  - "tests/**/*.php"
  - "phpunit.xml"
---

# PHP, migrations, tests

## DAL

Entity `blur_elysium_slides` + translation `blur_elysium_slides_translation`. Copy and media IDs live in translated `contentSettings` JSON. Language-chain merge is `ElysiumSlidesHydrator` + `ContentSettingsTrait`.

Do **not** reintroduce:

- Cover associations (`slideCover`, `slideCoverMobile`, `slideCoverTablet`, `slideCoverVideo`, `presentationMedia`)
- Translated columns `title`, `description`, `buttonLabel`, `url`

Store API `/store-api/elysium-slide` returns media IDs in `contentSettings`; consumers resolve media themselves.

## Migrations

New file: `src/Migration/Migration{unixTimestamp}Description.php` plus a test extending `tests/Migration/AbstractMigrationTestCase.php` (that case drops slide tables in `tearDown`).

Make schema changes idempotent with `Defaults::MIGRATION_*` regexes. Do not add an `isAlreadyMigrated()` helper (used in one legacy migration only).

```php
public function update(Connection $connection): void
{
    try {
        $connection->executeStatement('
            ALTER TABLE `blur_elysium_slides`
            ADD COLUMN `example` VARCHAR(255) NULL
        ');
    } catch (\Exception $e) {
        if (!preg_match(Defaults::MIGRATION_COLUMN_EXISTS, $e->getMessage())) {
            throw $e;
        }
    }
}
```

`src/Bootstrap/PostUpdate/Version210/` only runs when upgrading from plugin version < 2.0.0. New data fixes belong in a `Migration*`, not a new PostUpdate folder.

## Feature flags

Preview work goes behind `Defaults::FEATURES` and `Feature::isActive(...)`:

- `elysium_preview_elasticsearch`
- `elysium_preview_time_control`
- `elysium_preview_import_export`

Tests: `FeatureFlagTestTrait` or `Feature::fake([...], ...)`.

## PHPUnit

`phpunit.xml` suites: `migration`, `subscriber`, `service`, `command`, `demodata`.

From Shopware root:

```bash
APP_ENV=test ./vendor/bin/phpunit --configuration custom/plugins/BlurElysiumSlider/phpunit.xml
```

Local docker: `custom/static-plugins/BlurElysiumSlider/phpunit.xml`. Cloud schema repair: `FORCE_INSTALL=true` + `--testsuite migration`.
