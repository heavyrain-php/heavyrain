# syntax=docker/dockerfile:1
FROM php:8.2.11-cli-alpine3.18

ARG LANG=C.UTF-8
ENV LANG=${LANG}
ENV LC_ALL=${LANG}
ARG TZ=Asia/Tokyo
ENV TZ=${TZ}

CMD [ "php", "heavyrain" ]
