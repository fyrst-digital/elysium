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
SHOPWARE_VERSION="v6.7.8.2"

echo "==> [1/5] Installing Node dependencies"
cd "$PLUGIN_DIR"
npm ci

echo "==> [2/5] Preparing Shopware $SHOPWARE_VERSION at $SHOPWARE_ROOT"
if [ ! -d "$SHOPWARE_ROOT/.git" ]; then
    git clone --depth 1 --branch "$SHOPWARE_VERSION" \
        https://github.com/shopware/shopware.git "$SHOPWARE_ROOT"
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
# Composer 2.10+ blocks packages with advisories; the pinned Shopware
# release intentionally ships some, so opt out of the hard block.
composer config policy.advisories.block false
composer install --no-interaction --no-progress

echo "==> [5/5] Bootstrapping the test database"
bash "$PLUGIN_DIR/.cursor/start.sh"
# Install the Shopware test schema only if it is not already present, so
# re-running install stays fast and idempotent. We probe a core table
# (`migration`) rather than a plugin table, because the plugin's own
# migration tests drop their tables in tearDown.
if ! mariadb -uapp -papp -h127.0.0.1 -e "USE shopware_test; SHOW TABLES LIKE 'migration';" 2>/dev/null | grep -q migration; then
    FORCE_INSTALL=true APP_ENV=test ./vendor/bin/phpunit \
        --configuration custom/plugins/BlurElysiumSlider/phpunit.xml \
        --testsuite migration
fi

echo "==> Install complete."
