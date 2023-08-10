FROM nginx:stable

COPY ./templates/nginx.conf /etc/nginx/nginx.conf
COPY ./templates/localhost.conf /etc/nginx/conf.d/default.conf

VOLUME /var/run/sockets