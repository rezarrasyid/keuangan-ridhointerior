FROM php:7.4-apache

# Mengaktifkan modul rewrite URL (Penting untuk routing CI3)
RUN a2enmod rewrite

# Menginstall ekstensi database agar CI3 bisa konek ke MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Menyembunyikan informasi PHP dengan menonaktifkan expose_php (Tugas 4)
RUN echo "expose_php = Off" > /usr/local/etc/php/conf.d/security.ini

# Menyembunyikan informasi versi Apache Server (Tugas 4)
RUN echo "ServerTokens ProductOnly" >> /etc/apache2/apache2.conf \
    && echo "ServerSignature Off" >> /etc/apache2/apache2.conf