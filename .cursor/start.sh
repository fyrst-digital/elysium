#!/usr/bin/env bash
# Per-boot reconciliation: make sure MariaDB is running and reachable.
# Safe to run repeatedly. Durable data lives in /var/lib/mysql.
set -euo pipefail

DB_USER="app"
DB_PASS="app"

echo "[start] Ensuring MariaDB is running..."

sudo mkdir -p /var/run/mysqld /var/lib/mysql
sudo chown -R mysql:mysql /var/run/mysqld /var/lib/mysql

# Initialize the data directory the first time only.
if [ ! -d /var/lib/mysql/mysql ]; then
    echo "[start] Initializing MariaDB data directory..."
    sudo mariadb-install-db --user=mysql --datadir=/var/lib/mysql >/dev/null 2>&1 || true
fi

# Start the daemon if it is not already accepting connections.
if ! sudo mariadb -e "SELECT 1;" >/dev/null 2>&1; then
    sudo mariadbd-safe --datadir=/var/lib/mysql >/tmp/mariadb.log 2>&1 &
    for i in $(seq 1 30); do
        if sudo mariadb -e "SELECT 1;" >/dev/null 2>&1; then
            break
        fi
        sleep 1
    done
fi

if ! sudo mariadb -e "SELECT 1;" >/dev/null 2>&1; then
    echo "[start] ERROR: MariaDB did not become ready. See /tmp/mariadb.log" >&2
    exit 1
fi

# Ensure the TCP application user used by DATABASE_URL exists.
sudo mariadb <<SQL
CREATE USER IF NOT EXISTS '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON *.* TO '${DB_USER}'@'127.0.0.1' WITH GRANT OPTION;
FLUSH PRIVILEGES;
SQL

echo "[start] MariaDB is ready."
