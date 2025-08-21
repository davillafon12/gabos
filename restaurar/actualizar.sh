#!/bin/bash

if [ -z "$1" ]
  then
    echo "Debe pasar la direccion de la carpeta principal de GABO, como: ~/gabos"
    exit 1
fi

if [ -z "$2" ]
  then
    echo "Debe indicar si se debe recrear la imagen de configuracion de base de datos"
    exit 1
fi

if [ -z "$3" ]
  then
    echo $'Debe indicar el nombre del branch al que quiere actualizar, por ejemplo: produccion / pruebas.'
    exit 1
fi

cd $1

branch=$(cut -d':' -f1 <<< "$3")

echo "Actualizando repositorio..."
echo "    -> Haciendo git fecth"
git fetch --all
echo "    -> Pasandonos al branch/tag $branch"
git checkout $branch
echo "    -> Haciendo git pull"
git pull


echo "Construyendo nuevas imagenes..."

echo "    -> Haciendo re-build de la aplicacion de GABO"
docker compose build web-app

if [ $2 = "si" ]; then
  echo "    -> Haciendo re-build de la configuracion de la base de datos de GABO"
  docker compose build init-database
else
  echo "    -> OMITIR: re-build de la configuracion de la base de datos de GABO"
fi

echo "Actualizando el sistema..."
docker compose up -d

echo "Actualizacion lista!!!!"
