FROM php:8.2-fpm

# ១. ដំឡើង System Dependencies សម្រាប់ PostgreSQL និង Extension ដែលចាំបាច់
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql gd \
    && rm -rf /var/lib/apt/lists/*

# ២. ដំឡើង Node.js (សម្រាប់ដំណើរការ node-khqr ឬ Frontend assets)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && apt-get install -y nodejs

# ៣. ដំឡើង Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ៤. កំណត់ Working Directory
WORKDIR /app
COPY . .

# ៥. ដំឡើង PHP Dependencies និង Node Dependencies សម្រាប់ KHQR
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs \
    && if [ -d "node-khqr" ]; then cd node-khqr && npm install --omit=dev && cd ..; fi

# ៦. កំណត់ Permissions ឱ្យបានត្រឹមត្រូវសម្រាប់ Laravel Folders
RUN mkdir -p /app/storage/app/public /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views /app/storage/logs \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# ៧. បើក Port 8000
EXPOSE 8000

# ៨. ដំណើរការ Command បង្ហាញ App (ប្រើ db:wipe ដើម្បីកម្ចាត់ Transaction Error)
CMD ["sh", "-c", "php artisan storage:link --force || true && \
    php artisan db:wipe --force && \
    php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=8000"]
