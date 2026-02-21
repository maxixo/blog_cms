#!/usr/bin/env bash
set -euo pipefail

PORT="${PORT:-8080}"

# Bind Apache to Railway's injected PORT
sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s#<VirtualHost \*:80>#<VirtualHost *:${PORT}>#g" /etc/apache2/sites-available/000-default.conf

apache2-foreground
