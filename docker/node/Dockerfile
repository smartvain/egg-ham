FROM node:16.13.2-alpine

RUN apk update && \
    apk add git vim

WORKDIR /var/www/front

ENTRYPOINT ["yarn", "dev"]