#!/bin/bash

cd ~/gabos

echo "Bajando sistema..."
docker compose down

echo "Actualizando repositorio..."
git pull

echo "Construyendo nuevas imagenes..."
docker compose build web-app

echo "Levantando sistema..."
docker compose up -d

echo "Actualizacion lista!!!!"
