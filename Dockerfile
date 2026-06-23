# syntax=docker/dockerfile:experimental

# Etapa Base para PHP
FROM ubuntu:22.04 as base

ARG PHP_VERSION=8.4
ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    curl \
    gzip \
    ca-certificates \
    zip \
    unzip \
    git \
    software-properties-common \
    gnupg2 \
    && add-apt-repository ppa:ondrej/php \
    && apt-get update \
    && apt-get install -y php${PHP_VERSION}-cli php${PHP_VERSION}-fpm php${PHP_VERSION}-common \
       php${PHP_VERSION}-mysql php${PHP_VERSION}-zip php${PHP_VERSION}-gd php${PHP_VERSION}-mbstring \
       php${PHP_VERSION}-curl php${PHP_VERSION}-xml php${PHP_VERSION}-bcmath php${PHP_VERSION}-tokenizer \
       php${PHP_VERSION}-redis \
    && mkdir /run/php \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configuración de la APP
FROM base

RUN mkdir /app
WORKDIR /app

# Copiamos todo el proyecto local (incluye tu vendor y tu public/build)
COPY . .

# Permisos para Laravel
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

EXPOSE 8080

# Comando de inicio (Fly usa por defecto el entrypoint configurado en su binario o puedes dejar el tuyo si venía abajo)
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]