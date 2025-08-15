#!/bin/bash
set -e

# Create .env if missing
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# Install PHP deps if not installed
if [ ! -d "vendor" ]; then
    composer install --no-interaction --prefer-dist
fi

# Install Node deps if not installed
if [ ! -d "node_modules" ]; then
    npm install
fi

# Generate app key if missing
if ! grep -q "APP_KEY=" .env || [ -z "$(grep APP_KEY= .env | cut -d '=' -f2)" ]; then
    php artisan key:generate
fi

php artisan migrate --force
php artisan db:seed --force

# Ensure Laravel knows the correct Vite dev server URL
if ! grep -q "VITE_DEV_SERVER_URL=" .env; then
    echo "VITE_DEV_SERVER_URL=http://localhost:5174" >> .env
fi

# Start Laravel backend
php artisan serve --host=0.0.0.0 --port=8000 &

# Start Vite dev server on 0.0.0.0:5174
npm run dev -- --host 0.0.0.0 --port 5174
