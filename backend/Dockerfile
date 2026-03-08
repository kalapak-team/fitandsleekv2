FROM php:8.2-fpm

# ១. ដំឡើង Dependencies សម្រាប់ Postgres និង GD
RUN apt-get update && apt-get install -y \
    libpq-dev libpng-dev libjpeg-dev libfreetype6-dev zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql gd \
    && rm -rf /var/lib/apt/lists/*

# ២. ដំឡើង Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && apt-get install -y nodejs

# ៣. ដំឡើង Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# ៤. ដំឡើង Dependencies
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs \
    && cd /app/node-khqr && npm install --omit=dev && cd /app

# ៥. កំណត់ Permissions ឱ្យបានត្រឹមត្រូវ
RUN mkdir -p /app/storage/app/public /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views /app/storage/logs \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 8000

# ៦. បញ្ជាឱ្យបង្កើត Table ថ្មីទាំងអស់ (Fresh Migration)
CMD ["sh", "-c", "php artisan storage:link --force || true && php artisan migrate:fresh --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=8000"]
