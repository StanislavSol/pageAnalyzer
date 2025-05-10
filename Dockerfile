# Базовый образ с предустановленным Composer
FROM composer:2.6 AS builder

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Финальный образ
FROM php:8.2-fpm

# Установка зависимостей в ОДИН RUN
RUN apt-get update && \
    apt-get install -y \
        libpq-dev \
        libzip-dev \
        postgresql-client \
        unzip && \
    docker-php-ext-install pdo pdo_pgsql zip opcache && \
    rm -rf /var/lib/apt/lists/* && \
    mkdir -p /app/var && \
    chown -R www-data:www-data /app/var

# Копируем vendor из builder-стадии
COPY --from=builder /app/vendor /app/vendor

# Рабочая директория и копирование кода
WORKDIR /app
COPY . .

# Команда запуска с миграциями
CMD psql ${DATABASE_URL} -f database.sql && php-fpm
