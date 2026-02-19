FROM php:8.2-cli

RUN docker-php-ext-install mysqli

WORKDIR /app

COPY . .

EXPOSE 8080

CMD php -S 0.0.0.0:8080 -t /app
