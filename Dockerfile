# Set the base image
FROM php:8.1-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    supervisor \
    libpng-dev \
    && docker-php-ext-install zip gd

# Enable Apache rewrite module
RUN a2enmod rewrite

# Disable composer-plugin-interactive
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_PLUGIN_INTERACTIVE=0

# Copy application files
COPY . /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install application dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Copy Supervisor configuration file
COPY laravel-horizon.conf /etc/supervisor/conf.d/laravel-horizon.conf

# Set environment variables
ENV APP_NAME="JNT CASHBACK"
ENV APP_ENV=local
ENV APP_KEY=base64:e2Cef5WfCL+PDbOI39U2mPvgoHbjtF2xshUSgS1uC7E=
ENV APP_DEBUG=true
ENV APP_URL=http://jnt-cashback.localtest

ENV LOG_CHANNEL=stack
ENV LOG_DEPRECATIONS_CHANNEL=null
ENV LOG_LEVEL=debug

ENV DB_CONNECTION=pgsql
ENV DB_HOST=postgres
ENV DB_PORT=5432
ENV DB_DATABASE=jnt_express
ENV DB_USERNAME=postgres
ENV DB_PASSWORD=

ENV BROADCAST_DRIVER=log
ENV CACHE_DRIVER=redis
ENV FILESYSTEM_DISK=local
ENV QUEUE_CONNECTION=redis
ENV SESSION_DRIVER=file
ENV SESSION_LIFETIME=120

ENV MEMCACHED_HOST=127.0.0.1

ENV REDIS_CLIENT=predis
ENV REDIS_HOST=redis
ENV REDIS_PASSWORD=null
ENV REDIS_PORT=6379

ENV MAIL_MAILER=smtp
ENV MAIL_HOST=mailpit
ENV MAIL_PORT=1025
ENV MAIL_USERNAME=null
ENV MAIL_PASSWORD=null
ENV MAIL_ENCRYPTION=null
ENV MAIL_FROM_ADDRESS="hello@example.com"
ENV MAIL_FROM_NAME="${APP_NAME}"

ENV AWS_ACCESS_KEY_ID=
ENV AWS_SECRET_ACCESS_KEY=
ENV AWS_DEFAULT_REGION=us-east-1
ENV AWS_BUCKET=
ENV AWS_USE_PATH_STYLE_ENDPOINT=false

ENV PUSHER_APP_ID=
ENV PUSHER_APP_KEY=
ENV PUSHER_APP_SECRET=
ENV PUSHER_HOST=
ENV PUSHER_PORT=443
ENV PUSHER_SCHEME=https
ENV PUSHER_APP_CLUSTER=mt1

ENV VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
ENV VITE_PUSHER_HOST="${PUSHER_HOST}"
ENV VITE_PUSHER_PORT="${PUSHER_PORT}"
ENV VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
ENV VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

ENV LADMIN_LOGO_URL="/jnt.png"

# Expose port 80
EXPOSE 80

# Start Supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
