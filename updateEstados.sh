#!/bin/bash

RUTA_LOG="/var/log/gabos/updater/$(date +'%Y_%m_%d').log"
HOST_GABO=127.0.0.1
PUERTO_GABO=8181

URL_REENVIO="$HOST_GABO:$PUERTO_GABO/external/enviarComprobantesAHacienda"
URL_ACTUALIZAR="$HOST_GABO:$PUERTO_GABO/external/actualizarComprobantes"


echo "$(date +'%H:%M') | Enviando comprobantes!!!" >> $RUTA_LOG
echo "$(date +'%H:%M') | Executing: wget $URL_REENVIO >> $RUTA_LOG" >> $RUTA_LOG

wget "${URL_REENVIO}" -O ->> "${RUTA_LOG}"

echo "$(date +'%H:%M') | Enviar comprobantes | DONE" >> $RUTA_LOG



echo "$(date +'%H:%M') | Actualizando estados!!!" >> $RUTA_LOG
echo "$(date +'%H:%M') | Executing: wget $URL_ACTUALIZAR >> $RUTA_LOG" >> $RUTA_LOG

wget "${URL_ACTUALIZAR}" -O ->> "${RUTA_LOG}"

echo "$(date +'%H:%M') | Actualizar estados | DONE" >> $RUTA_LOG

