FROM nginx:1.24.0-alpine-slim

WORKDIR /etc/nginx/conf.d

COPY ./nginx/nginx.conf .

RUN mv nginx.conf default.conf

WORKDIR /var/www/html

COPY . .