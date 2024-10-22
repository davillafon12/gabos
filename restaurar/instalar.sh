#!/bin/bash

sudo apt update
sudo apt -y install rsync openssh-server sshpass ca-certificates curl git

if mount | grep /gabo/docker > /dev/null; then
    sudo mkdir /etc/docker
    sudo cat '{ "data-root":"/gabo/docker" }' > /etc/docker/daemon.json
    chmod 777 -R /etc/docker
else
    echo "El punto de montaje /gabo/docker no se encuentra, debe realizarlo antes de proceder con la instalacion"
    exit 1
fi

#Docker
if [ ! -x "$(command -v docker)" ]; then
    for pkg in docker.io docker-doc docker-compose podman-docker containerd runc; do sudo apt-get remove $pkg; done

    sudo install -m 0755 -d /etc/apt/keyrings
    sudo curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc
    sudo chmod a+r /etc/apt/keyrings/docker.asc

    # Add the repository to Apt sources:
    echo \
    "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/debian \
    $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
    sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
    sudo apt-get update

    sudo apt-get -y install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

    sudo getent group docker || sudo groupadd docker 
    sudo usermod -aG docker gabo_admin 
    echo "POR FAVOR REINICIAR SERVIDOR!!!"
    exit 1
fi

#Repositorio
if ! test -d ~/gabos; then
  git clone https://github.com/davillafon12/gabos.git
fi

cd ~/gabos

if ! test -f restaurar/jasper/gabo_jasper_reports_image.tar.gz; then
    wget -O restaurar/jasper/gabo_jasper_reports_image.tar.gz "https://www.dropbox.com/scl/fi/ug3pkqwkxgbwtubvcnts2/gabo_jasper_reports_image.tar.gz?rlkey=yzf1pln08l9584mc90qdwvhka&st=y5t605zq&dl=0"
    docker load -i restaurar/jasper/gabo_jasper_reports_image.tar.gz    
fi

set -a
source <(cat .env | \
    sed -e '/^#/d;/^\s*$/d' -e "s/'/'\\\''/g" -e "s/=\(.*\)/='\1'/g")
set +a

if [ -z "${USUARIO_DEBIAN_SSH_PASSWORD}" ]; then
   echo "Antes de seguir por favor llene el archivo .env con todos los valores requeridos"
   open .env
   exit 1
fi

if ! id "${AIRFLOW_VAR_SSH_DOCKER_HOST_USER}" >/dev/null 2>&1; then
    sudo useradd "${AIRFLOW_VAR_SSH_DOCKER_HOST_USER}"
    sudo usermod -aG sudo "${AIRFLOW_VAR_SSH_DOCKER_HOST_USER}"
    sudo echo "${AIRFLOW_VAR_SSH_DOCKER_HOST_USER}:${AIRFLOW_VAR_SSH_DOCKER_HOST_USER_PASSWORD}" | sudo chpasswd
else
    echo "User ${AIRFLOW_VAR_SSH_DOCKER_HOST_USER} already created. Skipping."
fi


if ! test -f restaurar/database/respaldo-procedimientos.sql; then
    echo "-------------------------------------------------------->>>>>>> ERROR!!!!"
    echo "------------->>> FALTA ARCHIVO: gabos/restaurar/database/respaldo-procedimientos.sql"
    echo "-------------------------------------------------------->>>>>>> ERROR!!!!"
    exit 1
fi

if ! test -f restaurar/database/respaldo-gabo.sql; then
    echo "-------------------------------------------------------->>>>>>> ERROR!!!!"
    echo "------------->>> FALTA ARCHIVO: gabos/restaurar/database/respaldo-gabo.sql"
    echo "------------->>> Por favor descargar el ultimo respaldo de la bd de Dropbox, descomprimirlo, renombrarlo y moverlo a la ubicacion respectiva"
    echo "-------------------------------------------------------->>>>>>> ERROR!!!!"
    exit 1
fi

touch restaurar/database/respaldo-airflow.sql
touch restaurar/database/respaldo-jasperreports.sql

if ! test -d restaurar/app/certificados; then
    echo "-------------------------------------------------------->>>>>>> ERROR!!!!"
    echo "------------->>> FALTA CARPETA: gabos/restaurar/app/certificados"
    echo "------------->>> Por favor descargar los certificados respaldados en Dropbox y colocarlos en esta carpeta"
    echo "-------------------------------------------------------->>>>>>> ERROR!!!!"
    exit 1
fi

if ! test -d restaurar/app/imagenes; then
    echo "-------------------------------------------------------->>>>>>> ERROR!!!!"
    echo "------------->>> FALTA CARPETA: gabos/restaurar/app/imagenes"
    echo "------------->>> Por favor descargar las imagenes respaldadas en Dropbox y colocarlos en esta carpeta"
    echo "-------------------------------------------------------->>>>>>> ERROR!!!!"
    exit 1
fi

cd ~/gabos
docker compose up -d


echo "------------------------------------>>> El sistema se esta creando, por favor dar unos 5mins antes de seguir con la instalacion"
echo "------------------------------------>>> Despues de los 5mins, vuelva a la guia de instalacion al punto 8 para configurar jasper reports"