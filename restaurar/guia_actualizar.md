# Guia para actualizar Gabo

## Que debo hacer antes de actualizar?
- Detener los DAGs habilitados de Airflow, para dejar de actualizar en Hacienda.
- Realizar un respaldo de la base de datos.
- Realizar un respaldo del archivo `.env` donde está toda la información sensible. Se debe respaldar en el servidor y en otro lugar externo al servidor.
- Saber si hay cambios que realizar en la base de datos, para ello vaya al archivo: [MODIFICACIONES.sql](https://github.com/davillafon12/gabos/blob/produccion/BD/Scripts/MODIFICACIONES.sql) y si tiene algo dentro, es porque hay que correr eso, pero aun no lo haga.
- Evitar actualizar en horas de trabajo para evitar discrepancias en los datos.
- Tener a mano el número de actualización actual y la que se desear usar. Por ejemplo: 2024.11.4.3 es la versión actual.
- Asegurarse que el respaldo de la base de datos este en otro lugar respaldado que no se el mismo servidor, como dropbox u otro disco externo.

## Script de actualización

### Obteniendo el script
Para obtener el script de actualización, por favor ejecutar el siguiente comando en cualquier carpeta, en la terminal:
```
wget https://github.com/davillafon12/gabos/raw/refs/heads/produccion/restaurar/actualizar.sh -v -O actualizar.sh && chmod 755 actualizar.sh
```
### Ejecutando el script
Para actualizar se utiliza el siguiente script:
```
./actualizar.sh RUTA_FOLDER_GABOS DESEA_RECREAR_APLICACION NUMERO_DE_VERSION_A_USAR
```
Donde:
- <b>RUTA_FOLDER_GABOS</b>: Es la carpeta donde esta el código de GABOS, por ejemplo `~/gabos` o `~/Documentos/git/gabos`
- <b>DESEA_RECREAR_APLICACION</b>: Aqui solo se pueden usar dos valores `si` o `no`, y este valor se utiliza para indicar que se desea recrear la aplicación de gabos, para la mayoría de los casos este valor debe ser `si`.
- <b>NUMERO_DE_VERSION_A_USAR</b>: Este es el número de versión de aplicación que se desea poner. Al realizar este archivo, la versión actual es la `2024.11.4.3`, cuyo formato es `Año.Mes.Dia.Incremento`.

## Actualizando
Tomando en cuenta que ya realizó los pasos anteriores, ya puede actualizar el app. Para eso imaginese que quiere actualizar a la versión `2024.11.4.3`, y no hay modificaciones de base de datos que ejecutar y necesita recrear la aplicación de gabos.

Solo debe ejecutar lo siguiente:
```
./actualizar.sh ~/gabos si 2024.11.4.3
```

Al final de la actualización, debería verse así:
```
Actualizando el sistema...
[+] Running 9/9
 ✔ Container gabos-database-1               Running                                                                          
 ✔ Container gabos-php-my-admin-1           Running                                                                          
 ✔ Container gabos-jasper-reports-server-1  Running                                                                          
 ✔ Container gabos-airflow-triggerer-1      Running                                                                          
 ✔ Container gabos-airflow-scheduler-1      Started                                                                          
 ✔ Container gabos-airflow-webserver-1      Started                                                                          
 ✔ Container gabos-web-app-1                Started                                                                          
 ✔ Container gabos-init-database-1          Exited                                                                           
 ✔ Container gabos-airflow-init-1           Exited                                                                           
Actualizacion lista!!!!
```