FROM php:8.3-apache

# Enable Apache modules commonly needed for PHP apps
RUN a2enmod rewrite headers

# Set DocumentRoot to the project's public folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public_html

# Update Apache configs to use the new DocumentRoot
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
 && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# (Optional) sensible PHP defaults; you can tune later
RUN { \
  echo "display_errors=On"; \
  echo "error_reporting=E_ALL"; \
  echo "log_errors=On"; \
} > /usr/local/etc/php/conf.d/dev.ini
