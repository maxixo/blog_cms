#!/usr/bin/env bash
set -euo pipefail

PORT_RAW="${PORT:-}"
PORT_RAW="${PORT_RAW//$'\r'/}"
PORT_RAW="${PORT_RAW//$'\n'/}"

if [[ "$PORT_RAW" =~ ^[0-9]{1,5}$ ]]; then
  PORT_NUM=$((10#$PORT_RAW))
  if (( PORT_NUM >= 1 && PORT_NUM <= 65535 )); then
    APP_PORT="$PORT_NUM"
  else
    APP_PORT="8080"
  fi
elif [[ "$PORT_RAW" =~ :([0-9]{1,5})$ ]]; then
  PORT_NUM=$((10#${BASH_REMATCH[1]}))
  if (( PORT_NUM >= 1 && PORT_NUM <= 65535 )); then
    APP_PORT="$PORT_NUM"
  else
    APP_PORT="8080"
  fi
else
  APP_PORT="8080"
fi

echo "PORT raw: '${PORT_RAW:-<empty>}'"
echo "Using Apache port: ${APP_PORT}"

# Ensure a single MPM is active. mod_php in php:apache requires prefork.
a2dismod -f mpm_event mpm_worker >/dev/null 2>&1 || true
a2enmod mpm_prefork >/dev/null 2>&1 || true

# Emit MPM directives to make startup issues diagnosable from Railway logs.
echo "=== Apache MPM directives ==="
grep -R "^[[:space:]]*LoadModule[[:space:]]\\+mpm_" /etc/apache2 2>/dev/null || true

# Bind Apache to Railway's injected PORT
sed -ri "s/^[[:space:]]*Listen[[:space:]]+[0-9]+/Listen ${APP_PORT}/g" /etc/apache2/ports.conf
sed -ri "s#<VirtualHost \*:[0-9]+>#<VirtualHost *:${APP_PORT}>#g" /etc/apache2/sites-available/000-default.conf

echo "=== Apache ports.conf ==="
cat /etc/apache2/ports.conf
echo "=== Apache vhost ==="
grep -n "<VirtualHost" /etc/apache2/sites-available/000-default.conf || true

apache2ctl -t
exec apache2-foreground
