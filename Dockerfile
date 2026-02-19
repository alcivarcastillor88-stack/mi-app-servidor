FROM php:8.2-cli

# Instalar extensión mysqli
RUN docker-php-ext-install mysqli

WORKDIR /app

COPY . .

EXPOSE 9000

CMD php -S 0.0.0.0:$PORT
