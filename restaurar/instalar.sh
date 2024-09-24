#!/bin/bash

sudo apt update
sudo apt -y install rsync openssh-server sshpass ca-certificates curl git

primeraVez="si"

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

    sudo apt-get y install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
fi


#Repositorio
if ! test -d ~/gabos; then
  git clone https://github.com/davillafon12/gabos.git
else
    primeraVez="no"
fi

if [ primeraVez = "si" ]; then
   echo "Antes de seguir por favor llene el archivo .env con todos los valores requeridos"
   exit 1
fi