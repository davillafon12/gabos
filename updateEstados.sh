#!/bin/bash

wget 127.0.0.1:8181/external/actualizarComprobantes -O ->> "/var/www/gabos/application/logs/updater_log_$(date +'%Y_%m_%d')"