# syntax=docker/dockerfile:1
FROM php:8.1-cli-bullseye

# 環境変数設定
ENV DEBIAN_FRONTEND=noninteractive
ARG LANG=C.UTF-8
ENV LANG=${LANG}
ENV LC_ALL=${LANG}
ARG TZ=Asia/Tokyo
ENV TZ=${TZ}

RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo ${TZ} > /etc/timezone \
    && cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

CMD [ "php", "heavyrain" ]
