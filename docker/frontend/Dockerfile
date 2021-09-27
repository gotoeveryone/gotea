FROM node:14.16

ENV APP_ROOT /var/app

WORKDIR ${APP_ROOT}
COPY package.json ${APP_ROOT}
COPY package-lock.json ${APP_ROOT}
COPY resources ${APP_ROOT}/resources

RUN npm install
