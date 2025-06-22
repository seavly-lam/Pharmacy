FROM ubuntu:24.04

# Set environment variables to prevent interactive prompts
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Phnom_Penh

# Update and upgrade system
RUN apt update && apt upgrade -y

# Set up timezone
RUN apt-get install -y tzdata && \
    ln -fs /usr/share/zoneinfo/Asia/Phnom_Penh /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata

# Install Node.js and npm (v18.x)
RUN apt-get install -y curl && \
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Install PHP dependencies
RUN apt-get update && apt-get install -y --no-install-recommends --quiet \
    git \
    libzip-dev \
    zip \
    unzip \
    ffmpeg \
    libxpm-dev \
    libwebp-dev \
    libgd-dev \
    zlib1g-dev \
    libbz2-dev \
    libpng-dev \
    libjpeg-dev \
    libicu-dev \
    libmcrypt-dev \
    build-essential \
    libreadline-dev \
    libfreetype6-dev \
    libmagickwand-dev \
    libjpeg-turbo8-dev \
    g++ \
    libonig-dev \
    libxml2-dev \
    imagemagick \
    nano \
    sudo

# Install nginx and PHP
RUN apt install -y nginx php8.3-fpm composer

# Install PHP extensions (note lowercase names)
RUN apt install -y \
    php8.3-pgsql \
    php8.3-imagick \
    php8.3-bcmath \
    php8.3-ctype \
    php8.3-fileinfo \
    php8.3-mbstring \
    php8.3-pdo \
    php8.3-tokenizer \
    php8.3-curl \
    php8.3-zip \
    php8.3-gd \
    php8.3-xml \
    php8.3-bz2

# Update php.ini configuration
RUN sed -i "s/^max_execution_time\s*=.*/max_execution_time = 600/" /etc/php/8.3/fpm/php.ini && \
    sed -i "s/^max_input_time\s*=.*/max_input_time = 600/" /etc/php/8.3/fpm/php.ini && \
    sed -i "s/^memory_limit\s*=.*/memory_limit = 1024M/" /etc/php/8.3/fpm/php.ini && \
    sed -i 's/upload_max_filesize\s*=.*/upload_max_filesize = 512M/' /etc/php/8.3/fpm/php.ini && \
    sed -i 's/post_max_size\s*=.*/post_max_size = 512M/' /etc/php/8.3/fpm/php.ini

# Install openssl and generate self-signed certificate
RUN apt install openssl -y && \
    mkdir /ssl && \
    cd /ssl && \
    openssl genrsa -out localhost.key 2048 && \
    openssl req -new -key localhost.key -out localhost.csr -subj "/C=US/ST=State/L=City/O=Organization/OU=Department/CN=localhost" && \
    openssl x509 -req -days 365 -in localhost.csr -signkey localhost.key -out localhost.crt

# Copy application files
COPY . /app
WORKDIR /app

# Install Composer dependencies
COPY .env.example .env
RUN composer install \
    --ignore-platform-reqs \
    --optimize-autoloader \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts

# Install npm dependencies and build assets
RUN npm install && \
    npm run build

# Generate application key and publish Livewire assets
RUN php artisan key:generate && \
    php artisan vendor:publish --force --tag=livewire:assets

# Configure Nginx
COPY localhost.conf /etc/nginx/sites-available/localhost.conf
RUN ln -s /etc/nginx/sites-available/localhost.conf /etc/nginx/sites-enabled && \
    rm /etc/nginx/sites-enabled/default && \
    ln -sf /dev/stdout /var/log/nginx/access.log && \
    ln -sf /dev/stderr /var/log/nginx/error.log

# Set permissions
RUN chown -R www-data:www-data /app && \
    chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 8000

CMD service php8.3-fpm start && nginx -g 'daemon off;'