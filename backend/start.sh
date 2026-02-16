#!/bin/sh
set -e

echo "🚀 Starting Laravel container..."
PORT_TO_USE=${PORT:-10000}
echo "Using port: $PORT_TO_USE"

# Ensure the config is in a place nginx actually reads
# We'll copy it to /etc/nginx/conf.d/default.conf (commonly included)
mkdir -p /etc/nginx/conf.d
cp /etc/nginx/sites-available/default /etc/nginx/conf.d/default.conf 2>/dev/null || true

# Update the listen port in both locations (sites-available and conf.d)
sed -i "s/listen 10000;/listen ${PORT_TO_USE};/g" /etc/nginx/sites-available/default 2>/dev/null || true
sed -i "s/listen 10000;/listen ${PORT_TO_USE};/g" /etc/nginx/conf.d/default.conf

echo "Starting PHP-FPM..."
php-fpm -D

# Test the configuration (now using conf.d)
echo "Checking nginx config..."
nginx -t

echo "Starting Nginx..."
exec nginx -g "daemon off;"
