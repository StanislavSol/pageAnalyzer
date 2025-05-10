# Базовый образ
FROM php:8.2-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    postgresql-client \  # Клиент PostgreSQL для миграций
    && docker-php-ext-install pdo pdo_pgsql zip opcache

# Установка Composer
COPY --from=composer:latest /usr/local/bin/composer /usr/local/bin/composer

# Рабочая директория
WORKDIR /app

# Копируем зависимости сначала для кэширования
COPY composer.json composer.lock ./

# Установка PHP зависимостей
RUN composer install --no-dev --no-scripts --optimize-autoloader

# Копируем весь код
COPY . .

# Установка прав
RUN chown -R www-data:www-data /app/var

# Entrypoint для миграций и запуска
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]

# Команда по умолчанию
CMD ["php-fpm"]
