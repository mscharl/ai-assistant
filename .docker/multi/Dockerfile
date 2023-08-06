ARG NODE_VERSION
ARG PHP_VERSION

FROM php:${PHP_VERSION}-fpm-alpine AS php-fpm

ARG BUILD_AGENT
ARG BUILD_TARGET="php-fpm"
ARG COMPOSE_PROJECT_NAME
ARG PHP_EXTENSIONS
ARG RUNTIME_PACKAGES

ENV COMPOSE_PROJECT_NAME="${COMPOSE_PROJECT_NAME}"
ENV PHP_IDE_CONFIG="serverName=${COMPOSE_PROJECT_NAME}"
ENV TRUSTED_PROXIES="127.0.0.1,REMOTE_ADDR"

RUN apk add --update --no-cache zsh figlet && \
    sed -i -e "s/bin\/ash/bin\/zsh/" /etc/passwd && \
    echo -e "figlet -t '${COMPOSE_PROJECT_NAME}' && cat /etc/docker-build" >> /etc/zsh/zshenv && \
    rm /etc/motd \
    ;

SHELL ["/bin/zsh", "-c"]

RUN declare RUNTIME_PACKAGE_ARRAY=(${=RUNTIME_PACKAGES}) && \
    apk add --update --no-cache "${RUNTIME_PACKAGE_ARRAY[@]}" \
    ;

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN mv "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini" && \
    chmod +x /usr/local/bin/install-php-extensions && \
    declare PHP_EXTENSION_ARRAY=(${=PHP_EXTENSIONS}) && \
    sh /usr/local/bin/install-php-extensions apcu opcache xdebug "${PHP_EXTENSION_ARRAY[@]}" \
    ;

COPY ./multi/php-fpm-conf.d/* ${PHP_INI_DIR}/conf.d/
COPY ./multi/php-fpm-fpm.d/* /usr/local/etc/php-fpm.d/
COPY ./multi/php-fpm-entrypoint.d/* /usr/local/bin/docker-php-entrypoint.d/
COPY ./multi/php-fpm-entrypoint/docker-php-entrypoint /usr/local/bin/

RUN chmod +x /usr/local/bin/docker-php-entrypoint.d/* && \
    chmod +x /usr/local/bin/docker-php-entrypoint \
    ;

RUN echo "${BUILD_TARGET}-${BUILD_AGENT}@$(date -Iseconds)" > /etc/docker-build;

WORKDIR "/var/data/websites/${COMPOSE_PROJECT_NAME}"

EXPOSE 9000

FROM node:${NODE_VERSION}-alpine AS node
FROM php-fpm AS cli

ARG BUILD_TARGET="cli"
ARG DEVELOP_PACKAGES
ARG NPM_PACKAGES

RUN zsh -c "$(curl -L https://github.com/deluan/zsh-in-docker/releases/download/v1.1.5/zsh-in-docker.sh)" -- \
    -t robbyrussell -p composer -p git \
    -p node -p npm -p nvm \
    -p symfony -p timer -p yarn \
    ;

RUN declare DEVELOP_PACKAGE_ARRAY=(${=DEVELOP_PACKAGES}) && \
    apk add --update --no-cache libstdc++ "${DEVELOP_PACKAGE_ARRAY[@]}" \
    ;

RUN curl "https://dl.cloudsmith.io/public/symfony/stable/config.alpine.txt?distro=alpine&version=$(cat /etc/alpine-release)" >> /etc/apk/repositories && \
    curl -1sLf "https://dl.cloudsmith.io/public/symfony/stable/rsa.1C204ECEF7BED6AB.key" > "/etc/apk/keys/stable@symfony-1C204ECEF7BED6AB.rsa.pub" && \
    apk add --update --no-cache symfony-cli \
    ;

COPY ./multi/php-cli-conf.d/* ${PHP_INI_DIR}/conf.d/
COPY ./multi/php-cli-entrypoint/docker-php-entrypoint /usr/local/bin/

RUN chmod +x /usr/local/bin/docker-php-entrypoint;

COPY --from=node /usr/local/lib/ /usr/local/lib/
COPY --from=node /opt/ /opt/
COPY --from=node /usr/local/bin/ /usr/local/bin/

RUN declare NPM_PACKAGE_ARRAY=(${=NPM_PACKAGES}) && \
    npm install -g "${NPM_PACKAGE_ARRAY[@]}" \
    ;

RUN echo "${BUILD_TARGET}-${BUILD_AGENT}@$(date -Iseconds)" > /etc/docker-build;

CMD ["php", "-a"]
