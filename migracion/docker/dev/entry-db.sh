#!/bin/bash

echo 'Iniciando base de datos...'
service mariadb start

echo 'Creando usuarios en BD...'
mysql -u root < /tmp/usuarios.sql

echo 'Realizando migracion de BD...'
{ echo 'use gabo_app;' ; cat /tmp/respaldo.sql ; } | mysql -uroot

echo 'Actualizando contraseña en BD...'
{ echo 'use gabo_app;' ; cat /tmp/cambio_contr.sql ; } | mysql -uroot

echo 'Eliminando archivos...'
rm /tmp/usuarios.sql
rm /tmp/respaldo.sql
rm /tmp/cambio_contr.sql

tail -f /var/log/*.log 
