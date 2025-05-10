# Используем официальный образ PHP с PostgreSQL поддержкой
FROM php:8.2-fpm

# Установка зависимостей для Render
RUN apt-get update && \
    apt-get install -y \
        libpq-dev \
        libzip-dev \
        postgresql-client-15 \  # Клиент для миграций
        unzip && \
    docker-php-ext-install pdo pdo_pgsql zip opcache && \
    rm -rf /var/lib/apt/lists/*

# Установка Composer
COPY --from=composer:latest /usr/local/bin/composer /usr/local/bin/composer

# Рабочая директория
WORKDIR /app

# Копируем зависимости отдельно для кэширования
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Копируем весь код
COPY . .

# Настройка прав (для Render)
RUN chmod -R 775 /app/var

# Применение миграций и запуск (специфика Render)
CMD psql ${DATABASE_URL} -f database.sql && php-fpm
