# Стадия сборки зависимостей
FROM composer:2.6 AS builder
WORKDIR /app
COPY composer.* ./
RUN composer install --no-dev --optimize-autoloader

# Финальный образ
FROM php:8.2-cli  # Используем cli вместо fpm для консольного приложения

# Установка зависимостей
RUN apt-get update && \
    apt-get install -y \
        libpq-dev \
        postgresql-client && \
    docker-php-ext-install pdo pdo_pgsql && \
    rm -rf /var/lib/apt/lists/*

# Копируем код и зависимости
WORKDIR /app
COPY --from=builder /app/vendor ./vendor
COPY . .

# Настройка порта (для Render)
EXPOSE 10000  # Render требует явное указание порта

# Команда запуска
CMD php -S 0.0.0.0:${PORT} -t public
