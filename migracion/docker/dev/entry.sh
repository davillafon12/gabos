#!/bin/bash

./var/www/gabos/createFolders.sh
service php5.6-fpm start
service apache2 start
service ssh start

tail -f /var/log/apache2/*.log