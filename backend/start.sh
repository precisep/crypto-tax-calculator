#!/bin/sh
set -e

echo "🚀 Starting Laravel container..."
PORT_TO_USE=${PORT:-10000}
echo "Using port: $PORT_TO_USE"

# Copy the config to the directory Nginx actually includes
mkdir -p /etc/nginx/conf.d
cp /etc/nginx/sites-available/default /etc/nginx/conf.d/default.conf

# Update the listen port in both places (for consistency)
sed -i "s/listen 10000;/listen ${PORT_TO_USE};/g" /etc/nginx/sites-available/default
sed -i "s/listen 10000;/listen ${PORT_TO_USE};/g" /etc/nginx/conf.d/default.conf

echo "Starting PHP-FPM..."
php-fpm -D

echo "Checking Nginx configuration..."
nginx -t

echo "Starting Nginx..."
exec nginx -g "daemon off;"
