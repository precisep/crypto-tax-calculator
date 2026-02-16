#!/bin/sh
set -e

echo "🚀 Starting Laravel container..."

PORT_TO_USE=${PORT:-10000}
echo "Using port: $PORT_TO_USE"

# Update nginx port dynamically
sed -i "s/listen 10000;/listen ${PORT_TO_USE};/g" /etc/nginx/sites-available/default

echo "Starting PHP-FPM..."
php-fpm -D

echo "Checking nginx config..."
nginx -t

echo "Starting Nginx..."
exec nginx -g "daemon off;"
