FROM php:8.2-fpm

# ១. ដំឡើង System Dependencies
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

# ២. ដំឡើង Node.js និង NPM
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# ៣. ដំឡើង Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ៤. កំណត់ទីតាំងការងារ (WORKDIR)
WORKDIR /app
COPY . .

# ៥. ដំឡើង PHP dependencies និង KHQR Node deps
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs \
    && cd /app/node-khqr \
    && npm install --omit=dev \
    && cd /app

# ៦. កំណត់សិទ្ធិ (Permissions)
# បង្កើត directory ឱ្យបានត្រឹមត្រូវ និងកំណត់ owner ទៅឱ្យ www-data
RUN mkdir -p /app/storage/app/public /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views /app/storage/logs \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# ៧. បើក Port
EXPOSE 8000

# ៨. ពាក្យបញ្ជាដំណើរការ (CMD) - កែសម្រួលត្រង់នេះ
# - ប្រើ --force លើ storage:link ដើម្បីកុំឱ្យវាបោះ error បើមាន link រួចហើយ
# - បន្ថែម "|| true" ដើម្បីឱ្យវាបន្តទៅមុខទៀត បើទោះជាមានបញ្ហាតូចតាច
CMD ["sh", "-c", "php artisan storage:link --force || true && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
