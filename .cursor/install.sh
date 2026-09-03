#!/usr/bin/env bash
# Idempotent repository bootstrap for the BlurElysiumSlider plugin.
#
# The plugin lives at /workspace but its test-suite (KernelTestBehaviour)
# needs a full Shopware installation. We clone the Shopware platform next to
# it and symlink the checked-out plugin into custom/plugins so tests, builds
# and shopware-cli all operate on the real source.
set -euo pipefail

PLUGIN_DIR="/workspace"
SHOPWARE_ROOT="${SHOPWARE_ROOT:-$HOME/shopware}"
SHOPWARE_VERSION="v6.7.13.1"
SHOPWARE_VERSION_CHANGED=false

current_shopware_version() {
    git -C "$SHOPWARE_ROOT" describe --tags --exact-match HEAD 2>/dev/null || true
}

echo "==> [1/5] Installing Node dependencies"
cd "$PLUGIN_DIR"
npm ci

echo "==> [2/5] Preparing Shopware $SHOPWARE_VERSION at $SHOPWARE_ROOT"
if [ ! -d "$SHOPWARE_ROOT/.git" ]; then
    git clone --depth 1 --branch "$SHOPWARE_VERSION" \
        https://github.com/shopware/shopware.git "$SHOPWARE_ROOT"
    SHOPWARE_VERSION_CHANGED=true
else
    current="$(current_shopware_version)"
    if [ "$current" != "$SHOPWARE_VERSION" ]; then
        echo "==> Switching Shopware from ${current:-unknown} to $SHOPWARE_VERSION"
        git -C "$SHOPWARE_ROOT" fetch --depth 1 origin tag "$SHOPWARE_VERSION"
        git -C "$SHOPWARE_ROOT" checkout --force -B "$SHOPWARE_VERSION" FETCH_HEAD
        SHOPWARE_VERSION_CHANGED=true
    fi
fi

cat > "$SHOPWARE_ROOT/.env" <<'ENV'
APP_ENV=test
APP_SECRET=devsecretdevsecretdevsecretdevse
APP_URL=http://localhost:8000
DATABASE_URL=mysql://app:app@127.0.0.1:3306/shopware
APP_DEBUG=1
BLUE_GREEN_DEPLOYMENT=0
MAILER_DSN=null://null
SHOPWARE_ES_ENABLED=0
SHOPWARE_ES_INDEXING_ENABLED=0
ENV

echo "==> [3/5] Linking plugin into custom/plugins"
mkdir -p "$SHOPWARE_ROOT/custom/plugins"
ln -sfn "$PLUGIN_DIR" "$SHOPWARE_ROOT/custom/plugins/BlurElysiumSlider"

echo "==> [4/5] Installing Shopware PHP dependencies"
cd "$SHOPWARE_ROOT"
# composer.lock is gitignored in the Shopware monorepo. A leftover lock
# from a previous pin (e.g. v6.7.8.2) will not satisfy v6.7.13.1, so drop
# it whenever the checkout actually changed.
if [ "$SHOPWARE_VERSION_CHANGED" = true ]; then
    rm -f composer.lock
fi
# Composer 2.10+ blocks packages with advisories. The v6.7.8.2 CI pin
# ships advisory-affected dompdf 3.1.4; opting out is harmless on
# v6.7.13.1 (dompdf 3.1.6) and keeps local installs aligned with CI.
composer config policy.advisories.block false
composer install --no-interaction --no-progress

echo "==> [5/5] Bootstrapping the test database"
bash "$PLUGIN_DIR/.cursor/start.sh"
# Rebuild the test schema when Shopware was just cloned or switched, or
# when the core `migration` table is missing. Plugin migration tests drop
# their own tables in tearDown, so we must not key off plugin tables.
needs_schema_install=false
if [ "$SHOPWARE_VERSION_CHANGED" = true ]; then
    needs_schema_install=true
elif ! mariadb -uapp -papp -h127.0.0.1 -e "USE shopware_test; SHOW TABLES LIKE 'migration';" 2>/dev/null | grep -q migration; then
    needs_schema_install=true
fi

if [ "$needs_schema_install" = true ]; then
    FORCE_INSTALL=true APP_ENV=test ./vendor/bin/phpunit \
        --configuration custom/plugins/BlurElysiumSlider/phpunit.xml \
        --testsuite migration
fi

echo "==> Install complete."
