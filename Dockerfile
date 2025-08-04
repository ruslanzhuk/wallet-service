FROM php:8.3-cli

# Встановлюємо системні залежності, curl, unzip, git
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    git \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libssl-dev \
    libxml2-dev \
    libgmp-dev \
    zlib1g-dev \
    protobuf-compiler \
    libicu-dev \
    libprotobuf-dev \
    zip \
    && docker-php-ext-install \
        zip \
        bcmath \
        gmp \
        sockets \
        pcntl

RUN docker-php-ext-install pdo_pgsql intl \
 && docker-php-ext-enable opcache

# Встановлюємо protobuf та grpc PHP розширення через pecl
RUN pecl install grpc protobuf \
    && docker-php-ext-enable grpc protobuf

# Встановлюємо composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# RoadRunner 2025.1.2
RUN curl -L https://github.com/roadrunner-server/roadrunner/releases/download/v2025.1.2/roadrunner-2025.1.2-linux-amd64.tar.gz \
    -o /tmp/rr.tar.gz \
 && tar -xzf /tmp/rr.tar.gz -C /tmp \
 && mv /tmp/roadrunner-2025.1.2-linux-amd64/rr /usr/local/bin/rr \
 && chmod +x /usr/local/bin/rr

WORKDIR /var/www/wallet-service

#COPY composer.* ./
#RUN composer install --no-interaction --no-progress
#
#COPY . .

COPY composer.* ./
RUN composer install --no-scripts --no-autoloader --no-interaction

COPY . .

RUN composer dump-autoload --optimize \
 && php bin/console cache:clear

COPY roadrunner.yaml ./

EXPOSE 50051
EXPOSE 8000

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

CMD ["entrypoint.sh"]

#CMD ["rr", "serve", "-c", "roadrunner.yaml"]
