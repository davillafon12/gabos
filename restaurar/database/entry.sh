#!/bin/bash

if ! test -f /etc/my.cnf.original; 
then
    cat /etc/my.cnf > /etc/my.cnf.original
else
    cat /etc/my.cnf.original > /etc/my.cnf
fi

sed -i "s#DB_GABO_RESPALDO_USER#${DB_GABO_RESPALDO_USER}#g" /etc/my.cnf
sed -i "s#DB_GABO_RESPALDO_PASSWORD#${DB_GABO_RESPALDO_PASSWORD}#g" /etc/my.cnf

echo 'Iniciando base de datos...'

service mariadb start

echo 'Creando usuario de airflow...'
#usuario para airflow
useradd -m ${AIRFLOW_SSH_USER}
usermod -aG sudo ${AIRFLOW_SSH_USER}
echo "${AIRFLOW_SSH_USER}:${AIRFLOW_SSH_USER_PASSWORD}" | chpasswd

service ssh start

tail -f /var/log/*.log 
