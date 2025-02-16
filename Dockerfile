# Usa una imagen con PHP 8.1 y Node.js para manejar Symfony y Encore
FROM php:8.1-fpm

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libicu-dev \
    libpq-dev \
    nodejs \
    npm \
    && docker-php-ext-install intl pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /var/www

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de Symfony y Frontend
RUN composer install --no-dev --optimize-autoloader
RUN yarn install && yarn encore production

# Configurar permisos de Symfony
RUN chmod -R 777 var/cache var/log var/sessions

# Exponer el puerto 8000 para Symfony Server
EXPOSE 8000

# Comando de inicio
CMD ["symfony", "server:start", "--no-interaction"]
