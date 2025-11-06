# Build stage for Node.js assets
FROM node:20-alpine AS node-builder

WORKDIR /app

# Copy package files
COPY package.json package-lock.json* ./

# Install npm dependencies
RUN npm install

# Copy application source
COPY . .

# Build assets
RUN npm run build

# Base image
FROM php:8.3-fpm

LABEL authors="santhoshj"

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libzip-dev

RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

RUN docker-php-ext-configure gd --enable-gd --with-jpeg
RUN docker-php-ext-install gd

RUN docker-php-ext-install exif

RUN apt install  zip libzip-dev
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

RUN docker-php-ext-install pdo pdo_mysql
RUN mkdir -p /usr/src/php/ext/redis; \
	curl -fsSL https://pecl.php.net/get/redis --ipv4 | tar xvz -C "/usr/src/php/ext/redis" --strip 1; \
	docker-php-ext-install redis;
# Install Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Copy built assets from node-builder stage
COPY --from=node-builder /app/public/build /var/www/html/public/build

# Copy application code
VOLUME /var/www/html

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
#CMD ["php", "artisan", "serve", "--port=8000", "--host=0.0.0.0"]
CMD ["php-fpm"]
