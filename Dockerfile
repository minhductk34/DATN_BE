# Sử dụng hình ảnh PHP chính thức
FROM php:8.2-fpm

# Cài đặt các tiện ích cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Sao chép mã nguồn vào container
COPY . /var/www/html

WORKDIR /var/www/html

# Cài đặt các phụ thuộc PHP
RUN composer install --no-dev --optimize-autoloader

# Mở cổng
EXPOSE 9000

CMD ["php-fpm"]
