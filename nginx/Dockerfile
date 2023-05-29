FROM symfony-php as php
RUN php bin/console assets:install

FROM nginx:1.21-alpine

COPY nginx/nginx.conf  /etc/nginx/
COPY nginx/templates /etc/nginx/templates/

COPY ./public /var/www/html/public
COPY --from=php /var/www/html/public/bundles /var/www/html/public/bundles

RUN echo "upstream php-upstream { server 127.0.0.1:9000; }" > /etc/nginx/conf.d/upstream.conf

EXPOSE 80
EXPOSE 443