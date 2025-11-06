# Multi-stage: Build with Node, then PHP/Apache
FROM node:20-alpine AS builder
WORKDIR /app
COPY package*.json yarn.lock ./
RUN yarn install
COPY . .
RUN yarn build

FROM php:8.2-apache
# Install system deps (Postgres support, unzip for composer)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_pgsql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY --from=builder /app ./
RUN composer install --no-dev --optimize-autoloader \
    && chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

# Copy env/config (use Render env vars)
COPY .env.example .env
RUN sed -i "s/mysql:/pgsql:/g" .env  # Quick Postgres swap

# Run migrations on start (via entrypoint)
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]

EXPOSE 80