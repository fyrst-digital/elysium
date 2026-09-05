# Cloud Agent base image for the BlurElysiumSlider Shopware 6.7 plugin.
# System-level, slow-changing toolchain lives here; repository-specific
# setup (npm/composer install, Shopware bootstrap) happens in install.sh.
FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1

# Core tooling + PHP 8.3 with the extensions Shopware 6.7 requires + MariaDB.
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates curl git unzip jq gnupg sudo \
        mariadb-server mariadb-client \
        php8.3-cli php8.3-common php8.3-curl php8.3-gd php8.3-intl \
        php8.3-mbstring php8.3-mysql php8.3-xml php8.3-zip php8.3-bcmath \
        php8.3-opcache php8.3-soap php8.3-readline \
    && rm -rf /var/lib/apt/lists/*

# Node.js 20 (matches CI) via NodeSource.
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# Composer.
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm -f /tmp/composer-setup.php

# shopware-cli (pinned to the version used in CI).
ARG SHOPWARE_CLI_VERSION=0.15.2
RUN curl -sSL "https://github.com/shopware/shopware-cli/releases/download/${SHOPWARE_CLI_VERSION}/shopware-cli_linux_x86_64.tar.gz" \
        -o /tmp/shopware-cli.tar.gz \
    && tar -xzf /tmp/shopware-cli.tar.gz -C /usr/local/bin shopware-cli \
    && chmod +x /usr/local/bin/shopware-cli \
    && rm -f /tmp/shopware-cli.tar.gz

# Cloud Agents run as the "ubuntu" user (uid 1000 already exists on 24.04).
RUN echo "ubuntu ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/90-ubuntu \
    && chmod 0440 /etc/sudoers.d/90-ubuntu \
    && mkdir -p /var/run/mysqld \
    && chown -R mysql:mysql /var/run/mysqld /var/lib/mysql

USER ubuntu
WORKDIR /workspace
