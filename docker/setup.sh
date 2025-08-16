#!/bin/bash
set -e

echo "[Setup] Starting initial setup..."

# Install PHP deps if not installed
if [ ! -d "vendor" ]; then
    echo "[Setup] Installing PHP dependencies..."
    composer install --no-interaction --prefer-dist
else
    echo "[Setup] PHP dependencies already installed."
fi

# Install Node deps if not installed
if [ ! -d "node_modules" ]; then
    echo "[Setup] Installing Node.js dependencies..."
    npm install
else
    echo "[Setup] Node.js dependencies already installed."
fi

# Generate app key if missing
if ! grep -q "APP_KEY=" .env || [ -z "$(grep APP_KEY= .env | cut -d '=' -f2)" ]; then
    echo "[Setup] Generating Laravel APP_KEY..."
    php artisan key:generate
else
    echo "[Setup] APP_KEY already set."
fi

# Ensure Laravel knows the correct Vite dev server URL
if ! grep -q "VITE_DEV_SERVER_URL=" .env; then
    echo "[Setup] Adding VITE_DEV_SERVER_URL to .env..."
    echo "VITE_DEV_SERVER_URL=http://localhost:5174" >> .env
else
    echo "[Setup] VITE_DEV_SERVER_URL already exists in .env."
fi

# Run migrations and seed database once
echo "[Setup] Running migrations..."
php artisan migrate --force

echo "[Setup] Seeding database..."
php artisan db:seed --force

echo "[Setup] Initial setup complete."
