# Стадия 1: Установка зависимостей через Composer
FROM composer:2.6 as builder

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Стадия 2: Финальный образ
FROM php:8.2-cli

# Установка системных зависимостей
RUN apt-get update && \
    apt-get install -y \
        libpq-dev \
        postgresql-client \
        unzip && \
    docker-php-ext-install pdo pdo_pgsql && \
    rm -rf /var/lib/apt/lists/* && \
    mkdir -p /app/var && \
    chown -R www-data:www-data /app/var

# Копирование vendor из builder-стадии
COPY --from=builder /app/vendor /app/vendor

# Копирование основного кода
WORKDIR /app
COPY . .

# Порт для Render
EXPOSE 10000

# Команда запуска
CMD sh -c "psql -a -d $DATABASE_URL -f database.sql && php -S 0.0.0.0:\$PORT -t public"
