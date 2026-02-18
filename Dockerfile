FROM php:8.2-cli

WORKDIR /app

COPY . .

EXPOSE 9000

CMD php -S 0.0.0.0:$PORT -t /app
