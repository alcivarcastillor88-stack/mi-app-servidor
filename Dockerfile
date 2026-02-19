FROM php:8.2-cli

RUN docker-php-ext-install mysqli

WORKDIR /app
COPY . .

EXPOSE 8080

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080}"]
