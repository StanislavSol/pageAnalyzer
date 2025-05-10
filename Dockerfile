# Базовый образ
FROM php:8.2-fpm

# Установка зависимостей в ОДНУ команду RUN
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    postgresql-client && \
    docker-php-ext-install pdo pdo_pgsql zip opcache && \
    rm -rf /var/lib/apt/lists/*

# Установка Composer
COPY --from=composer:latest /usr/local/bin/composer /usr/local/bin/composer

# Рабочая директория
WORKDIR /app

# Копирование зависимостей и установка
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Копирование всего кода
COPY . .

# Настройка прав
RUN chown -R www-data:www-data /app/var

# Команда для Render (миграции + запуск)
CMD psql $DATABASE_URL -f database.sql && php-fpm
