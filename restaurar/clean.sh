#!/bin/bash

echo "Limpiando ambiente..."

if [ $1 = "respaldar" ]; then
    echo "Realizando respaldo..."
    cp ~/gabos/restaurar/database/respaldo-gabo.sql /tmp/respaldo-gabo.sql
    cp -r ~/gabos/restaurar/app/certificados /tmp/certificados
    cp -r ~/gabos/restaurar/app/imagenes /tmp/imagenes
    cp -r ~/gabos/.env /tmp/.env
fi

cd ~/gabos
echo "Eliminando docker compose..."
docker compose down --rmi local --volumes

cd ~

if test -f dropbox_2024.04.17_amd64.deb; then
    echo "Eliminando instalador de dropbox"
    rm ~/dropbox_2024.04.17_amd64.deb    
fi

sudo rm -R ~/gabos

sudo apt-get -y remove --purge docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
sudo apt -y remove --purge rsync openssh-server sshpass ca-certificates curl git
sudo apt -y autoremove

sudo rm -rf /gabo/docker /etc/docker
sudo rm /etc/apparmor.d/docker
sudo groupdel docker
sudo rm -rf /var/run/docker.sock
sudo rm -rf /var/lib/containerd
sudo rm -r ~/.docker
sudo rm /etc/docker/daemon.json
