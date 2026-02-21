#!/usr/bin/env bash
set -euo pipefail

PORT="${PORT:-8080}"

# Ensure a single MPM is active. mod_php in php:apache requires prefork.
a2dismod -f mpm_event mpm_worker >/dev/null 2>&1 || true
a2enmod mpm_prefork >/dev/null 2>&1 || true

# Emit MPM directives to make startup issues diagnosable from Railway logs.
echo "=== Apache MPM directives ==="
grep -R "^[[:space:]]*LoadModule[[:space:]]\\+mpm_" /etc/apache2 2>/dev/null || true

# Bind Apache to Railway's injected PORT
sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s#<VirtualHost \*:80>#<VirtualHost *:${PORT}>#g" /etc/apache2/sites-available/000-default.conf

apache2ctl -t
exec apache2-foreground
