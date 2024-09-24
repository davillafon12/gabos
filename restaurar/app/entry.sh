#!/bin/bash

if ! test -f /var/www/gabos/application/config/database.original; 
then
    cat /var/www/gabos/application/config/database.php > /var/www/gabos/application/config/database.original
else
    cat /var/www/gabos/application/config/database.original > /var/www/gabos/application/config/database.php
fi

sed -i "s/DATABASE_USER_PASS/${GABO_APP_DB_PASSWORD}/g" /var/www/gabos/application/config/database.php
sed -i "s/DATABASE_USER/${GABO_APP_DB_USER}/g" /var/www/gabos/application/config/database.php
sed -i "s/DATABASE_NAME/${GABO_APP_DB_NAME}/g" /var/www/gabos/application/config/database.php

if ! test -f /var/www/gabos/application/libraries/Correo.original; 
then
    cat /var/www/gabos/application/libraries/Correo.php > /var/www/gabos/application/libraries/Correo.original
else
    cat /var/www/gabos/application/libraries/Correo.original > /var/www/gabos/application/libraries/Correo.php
fi

sed -i "s/CORREO_SMTP_URL/${CORREO_SMTP_URL}/g" /var/www/gabos/application/libraries/Correo.php
sed -i "s/CORREO_EMAIL_USER_PASSWORD/${CORREO_EMAIL_USER_PASSWORD}/g" /var/www/gabos/application/libraries/Correo.php
sed -i "s/CORREO_EMAIL_USER/${CORREO_EMAIL_USER}/g" /var/www/gabos/application/libraries/Correo.php
sed -i "s/CORREO_SMTP_PORT/${CORREO_SMTP_PORT}/g" /var/www/gabos/application/libraries/Correo.php

if ! test -f /var/www/gabos/application/controllers/reportes/reportes.original; 
then
    cat /var/www/gabos/application/controllers/reportes/reportes.php > /var/www/gabos/application/controllers/reportes/reportes.original
else
    cat /var/www/gabos/application/controllers/reportes/reportes.original > /var/www/gabos/application/controllers/reportes/reportes.php
fi

sed -i "s#REPORTES_URL_INTERNA#${REPORTES_URL_INTERNA}#g" /var/www/gabos/application/controllers/reportes/reportes.php
sed -i "s#REPORTES_URL_EXTERNA#${REPORTES_URL_EXTERNA}#g" /var/www/gabos/application/controllers/reportes/reportes.php
sed -i "s/REPORTES_SUCURSAL_GAROTAS_NUMERO/${REPORTES_SUCURSAL_GAROTAS_NUMERO}/g" /var/www/gabos/application/controllers/reportes/reportes.php
sed -i "s/REPORTES_USUARIO_JASPERREPORTS_PASSWORD_CODIFICADO/${REPORTES_USUARIO_JASPERREPORTS_PASSWORD_CODIFICADO}/g" /var/www/gabos/application/controllers/reportes/reportes.php
sed -i "s/REPORTES_USUARIO_JASPERREPORTS/${JASPERREPORT_DB_CONSULTA_USER}/g" /var/www/gabos/application/controllers/reportes/reportes.php

#usuario general para ssh
useradd -m ${USUARIO_DEBIAN_SSH}
usermod -aG sudo ${USUARIO_DEBIAN_SSH}
echo "${USUARIO_DEBIAN_SSH}:${USUARIO_DEBIAN_SSH_PASSWORD}" | chpasswd

#usuario para airflow
useradd -m -G www-data ${AIRFLOW_SSH_USER}
usermod -aG sudo ${AIRFLOW_SSH_USER}
echo "${AIRFLOW_SSH_USER}:${AIRFLOW_SSH_USER_PASSWORD}" | chpasswd

# To remove default site
if test -f /etc/apache2/sites-available/000-default.conf; 
then
    cat /etc/apache2/sites-available/000-default.conf > /etc/apache2/sites-available/000-default.conf.bk
    rm /etc/apache2/sites-available/000-default.conf
fi

./var/www/gabos/createFolders.sh

chown -R www-data:www-data /var/www

service php5.6-fpm start
service php8.2-fpm start
service apache2 start
service ssh start

tail -f /var/log/apache2/*.log