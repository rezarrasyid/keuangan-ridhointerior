FROM php:7.4-apache

# Mengaktifkan modul rewrite URL (Penting untuk routing CI3)
RUN a2enmod rewrite

# Menginstall ekstensi database agar CI3 bisa konek ke MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql