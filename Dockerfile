FROM debian:12

# docker build -t gabos . -f Dockerfile-app
# docker run -dit -p 80:80 -p 2222:22 gabos

RUN apt update
RUN apt -y install wget gnupg2 ca-certificates apt-transport-https openssh-server net-tools sshpass rsync

RUN wget -q https://packages.sury.org/php/apt.gpg -O- | apt-key add -
RUN echo "deb https://packages.sury.org/php/ bookworm main" | tee /etc/apt/sources.list.d/php.list
RUN apt update

RUN apt install -y php5.6 php5.6-fpm apache2 libapache2-mod-fcgid
RUN a2enmod actions fcgid alias proxy_fcgi rewrite ssl

RUN apt install -y php5.6-mysql php5.6-mcrypt php5.6-mbstring php5.6-curl php5.6-dom

RUN apt install -y php8.2 php8.2-fpm php8.2-mcrypt php8.2-mbstring php8.2-curl php8.2-dom

COPY restaurar/app/gabo.conf /etc/apache2/sites-available/gabo.conf
COPY restaurar/app/firmador.conf /etc/apache2/sites-available/firmador.conf

COPY . /var/www/gabos
RUN a2ensite gabo
RUN a2ensite firmador
RUN chmod 755 /var/www/gabos/createFolders.sh

COPY restaurar/app/php-5-6.ini /etc/php/5.6/fpm/php.ini
COPY restaurar/app/openssl.cnf /etc/ssl/openssl.cnf
COPY restaurar/app/openssl.cnf /usr/lib/ssl/openssl.cnf

COPY restaurar/app/entry.sh /usr/entry.sh
RUN chmod 755 /usr/entry.sh

RUN mkdir /var/run/sshd

COPY restaurar/app/certificados /var/www/gabos/application/third_party/certificados
COPY restaurar/app/imagenes /var/www/gabos/application/images/articulos

EXPOSE 80
EXPOSE 22
ENTRYPOINT ["bash", "/usr/entry.sh"]