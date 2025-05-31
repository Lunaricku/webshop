FROM php:8.2-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libzip-dev unzip \
    && docker-php-ext-install pdo pdo_mysql

# Enable mod_rewrite
RUN a2enmod rewrite

# Copy source code
COPY . /var/www/html

# Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer
RUN composer install

# Apache config for Slim
RUN echo '<Directory "/var/www/html/public">\n\
    AllowOverride All\n\
</Directory>' >> /etc/apache2/apache2.conf
# Require all granted\n\


# Set DocumentRoot to public/
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# RUN service apache2 restart
# RUN chown -R www-data:www-data /var/www/html
