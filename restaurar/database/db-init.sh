#!/bin/bash

echo '1) Iniciando base de datos'
service mariadb start

echo '2) Eliminando acceso remoto a root...'
{ echo "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');"; } | mysql -uroot
{ echo "FLUSH PRIVILEGES;"; } | mysql -uroot


echo '3) Creando bases de datos...'
{ echo "CREATE DATABASE IF NOT EXISTS ${GABO_APP_DB_NAME};"; } | mysql -uroot
{ echo "CREATE DATABASE IF NOT EXISTS ${AIRFLOW_DB_NAME};"; } | mysql -uroot
{ echo "CREATE DATABASE IF NOT EXISTS ${JASPERREPORT_DB_NAME};"; } | mysql -uroot


echo '4) Creando usuarios en BD...'
{ echo "CREATE USER IF NOT EXISTS '${DB_GABO_ADMIN_USER}'@'%' IDENTIFIED BY '${DB_GABO_ADMIN_PASSWORD}';"; } | mysql -uroot
{ echo "GRANT ALL ON *.* TO '${DB_GABO_ADMIN_USER}'@'%'  WITH GRANT OPTION;"; } | mysql -uroot

{ echo "CREATE USER IF NOT EXISTS '${GABO_APP_DB_USER}'@'%' IDENTIFIED BY '${GABO_APP_DB_PASSWORD}';"; } | mysql -uroot
{ echo "GRANT SELECT, INSERT, UPDATE, DELETE ON ${GABO_APP_DB_NAME}.* TO '${GABO_APP_DB_USER}'@'%';"; } | mysql -uroot

{ echo "CREATE USER IF NOT EXISTS '${AIRFLOW_DB_USER}'@'%' IDENTIFIED BY '${AIRFLOW_DB_PASSWORD}';"; } | mysql -uroot
{ echo "GRANT ALL ON ${AIRFLOW_DB_NAME}.* TO '${AIRFLOW_DB_USER}'@'%'  WITH GRANT OPTION;"; } | mysql -uroot

{ echo "CREATE USER IF NOT EXISTS '${JASPERREPORT_DB_USER}'@'%' IDENTIFIED BY '${JASPERREPORT_DB_PASSWORD}';"; } | mysql -uroot
{ echo "GRANT ALL ON ${JASPERREPORT_DB_NAME}.* TO '${JASPERREPORT_DB_USER}'@'%'  WITH GRANT OPTION;"; } | mysql -uroot

{ echo "CREATE USER IF NOT EXISTS '${JASPERREPORT_DB_CONSULTA_USER}'@'%' IDENTIFIED BY '${JASPERREPORT_DB_CONSULTA_PASSWORD}';"; } | mysql -uroot
{ echo "GRANT SELECT, EXECUTE ON ${GABO_APP_DB_NAME}.* TO '${JASPERREPORT_DB_CONSULTA_USER}'@'%'  WITH GRANT OPTION;"; } | mysql -uroot

{ echo "CREATE USER IF NOT EXISTS '${DB_GABO_RESPALDO_USER}'@'localhost' IDENTIFIED BY '${DB_GABO_RESPALDO_PASSWORD}';"; } | mysql -uroot
{ echo "GRANT SELECT, LOCK TABLES ON ${GABO_APP_DB_NAME}.* TO '${DB_GABO_RESPALDO_USER}'@'localhost' WITH GRANT OPTION;"; } | mysql -uroot
{ echo "GRANT SELECT, LOCK TABLES ON ${AIRFLOW_DB_NAME}.* TO '${DB_GABO_RESPALDO_USER}'@'localhost' WITH GRANT OPTION;"; } | mysql -uroot
{ echo "GRANT SELECT, LOCK TABLES ON ${JASPERREPORT_DB_NAME}.* TO '${DB_GABO_RESPALDO_USER}'@'localhost' WITH GRANT OPTION;"; } | mysql -uroot

echo '5) Cargando migraciones...'

procedimiento_gabo=$(mysql information_schema -N -uroot -se "select ROUTINE_BODY from routines where ROUTINE_NAME like 'PA_ArticulosExentos';")
echo "#### $procedimiento_gabo"
if [ -z "$procedimiento_gabo" ];
then
    if test -f /tmp/respaldo-procedimientos.sql; then
        echo '   -> Cargando procedimientos gabo db...'
        { echo "use ${GABO_APP_DB_NAME};" ; cat /tmp/respaldo-procedimientos.sql ; } | mysql -uroot
        echo '         Eliminando respaldo...'
        rm /tmp/respaldo-procedimientos.sql
    fi
else
    echo '   -> Respaldo de procedimientos no se cargo pues ya existe...'
fi

#airflow_tabla=$(mysql ${AIRFLOW_DB_NAME} -N -uroot -se "SHOW TABLES LIKE 'dag';")
#echo "#### $airflow_tabla"
#if [ -z "$airflow_tabla" ];
#then
#    if test -f /tmp/respaldo-airflow.sql; then
#        echo '   -> Cargando airflow db...'
#        { echo "use ${AIRFLOW_DB_NAME};" ; cat /tmp/respaldo-airflow.sql ; } | mysql -uroot
#        echo '         Eliminando respaldo...'
#        rm /tmp/respaldo-airflow.sql
#    fi
#else
#    echo '   -> Respaldo de airflow no se cargo pues ya existe...'
#fi
#
#jasperreport_tabla=$(mysql ${JASPERREPORT_DB_NAME} -N -uroot -se "SHOW TABLES LIKE 'jireportjob';")
#echo "#### $jasperreport_tabla"
#if [ -z "$jasperreport_tabla" ];
#then
#    if test -f /tmp/respaldo-jasperreports.sql; then
#        echo '   -> Cargando jasperreports db...'
#        { echo "use ${JASPERREPORT_DB_NAME};" ; cat /tmp/respaldo-jasperreports.sql ; } | mysql -uroot
#        echo '         Eliminando respaldo...'
#        rm /tmp/respaldo-jasperreports.sql
#    fi
#else
#    echo '   -> Respaldo de jasper reports no se cargo pues ya existe...'
#fi

tabla_gabo=$(mysql ${GABO_APP_DB_NAME} -N -uroot -se "SHOW TABLES LIKE 'catalogo_cabys';")
echo "#### $tabla_gabo"
if [ -z "$tabla_gabo" ];
then
    if test -f /tmp/respaldo-gabo.sql; then
        echo '   -> Cargando datos gabo db...'
        { echo "use ${GABO_APP_DB_NAME};" ; cat /tmp/respaldo-gabo.sql ; } | mysql -uroot
        echo '         Eliminando respaldo...'
        rm /tmp/respaldo-gabo.sql
    fi
else
    echo '   -> Respaldo de gabo app no se cargo pues ya existe...'
fi

echo 'Aplicando configuraciones globales en BD...'
{ echo "SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));"; } | mysql -uroot





