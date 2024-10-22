# Guia para restaurar Gabo

1) Instalar Debian.
2) Agregar al usuario `gabo_admin` y hacer la `Guia agregar sudoer`.
3) Realizar guia de disco si hay mas de dos discos
4) Instalar dropbox
5) Correr esto en terminal: `wget https://github.com/davillafon12/gabos/raw/refs/heads/produccion/restaurar/instalar.sh -v -O instalar.sh && chmod 755 instalar.sh && ./instalar.sh;`.
6) Al terminar esta parte se requiere reiniciar el servidor. Una vez reiniciado ejecutar esto en la terminal `cd ~ && ./instalar.sh` para continuar con la instalacion.
7) Al terminar se va a abrir una ventana con el archivo de texto `.env`, aqui debe agregar todos los usuarios y contraseñas.
8) Correr de nuevo la instalacion corriendo el comando: `~./instalar.sh`. En este punto se va a pedir que reinicie el servidor.
9) Descargar el ultimo respaldo de gabo de Dropbox, descomprimirlo, renombrarlo a `respaldo-gabo.sql` y colocarlo en la carpeta `gabos/restaurar/database/`.
10) Descargar el contenido de la carpeta de certificados de Dropbox (Documentos electronicos) y colocar los certificados en la carpeta `gabos/restaurar/app/certificados` (Si la carpeta no existe, crearla).
11) Descargar el contenido de la carpeta de imagenes de Dropbox y colocar las imagenes en la carpeta `gabos/restaurar/app/imagenes` (Si la carpeta no existe, crearla).
12) Correr el siguiente comando en la terminal `./instalar.sh`
13) Esperar unos 5 minutos a que todo corra y luego vayase a `localhost:8186/jasperserver` y acceda con el usuario y contraseña que ingreso en `.env`
14) Una vez dentro del servidor de jasper, vayase a `Administrar` -> `Ajustes del servidor` -> `Importar`, una vez ahi seleccione el archivo `export.zip` que esta en `gabos/restaurar/jasper`; luego seleccionar la opcion que dice `Clave Heredada` y por ultimo darle al boton de `Importar`. Con este paso se cargaran todos los reportes y configuracion al jasper report server.
15) Hay que actualizar la informacion de la base de datos en el jasper report server, para eso dele click al boton que es como una casita (En la parte de arriba-izquierda), en la barra del lado izquierdo dele click a `Data Sources` y se lo mostrara una que se llama `GaboProduccion`, dele click derecho y luego `Editar`. Una vez ahi ingrese los datos de la siguiente manera:
```
Anfitrión (obligatorio): gabo-database
Puerto (obligatorio): 3306
Base de datos (obligatorio): PONER AQUI EL NOMBRE DE LA BASE DE DATOS QUE SE USO EN .env (GABO_APP_DB_NAME)
URL (obligatorio): Dejar este campo como esta
Nombre de usuario: PONER AQUI EL USUARIO DE REPORTES QUE SE USO EN .env (JASPERREPORT_DB_CONSULTA_USER)
Contraseña: PONER AQUI LA CONTRASEÑA DE REPORTES QUE SE USO EN .env (JASPERREPORT_DB_CONSULTA_PASSWORD)
```
Una vez ingreso esos datos puede darle al boton `Guardar`, luego darle `Guardar` de nuevo.

16) Asegurarse que el usuario creado para rsync tenga membresia al grupo que trabaja con dropbox en la maquina host, si no rsync falla al sincronizar documentos electronicos

## Guia agregar sudoer
1) Ingresar como root corriendo lo siguiente `su`. Usar password de root.
2) Ejecutar luego `sudo usermod -aG sudo gabo_admin`.
3) Abrir el archivo `/etc/sudoers` y agregar esta linea `gabo_admin ALL=(ALL:ALL) ALL` despues de la linea `root    ALL=(ALL:ALL) ALL`. Guardar los cambios.
4) Ejectuar `exit` y seguir con la guia de instalacion.

## Guia de disco
1) Correo `lsblk` para ver que discos hay disponibles y anotar cual es el disco secundario (Es el disco que no tiene asignado root) EL disco asignado a root se vera como asi: 
```NAME   MAJ:MIN RM   SIZE RO TYPE MOUNTPOINTS
sda      8:0    0 894,3G  0 disk 
├─sda1   8:1    0 893,3G  0 part /
├─sda2   8:2    0     1K  0 part 
└─sda5   8:5    0   975M  0 part [SWAP]
```
El principal tiene asignado `/` y `[SWAP]`

2) El disco secundario se veria asi (Sin nada asignado):
```
sdb      8:16   0 894,3G  0 disk 
├─sdb1   8:17   0 893,3G  0 part 
├─sdb2   8:18   0     1K  0 part 
└─sdb5   8:21   0   975M  0 part 
```
3) Una vez localizado el disco secundario (sdb1 en el ejemplo anterior) se debe obtener el UUID del mismo (UUID = Identificacion del disco). Para obtener el UUID correr el siguiente comando: `lsblk -f`. Una vez corra ese comando se vera algo asi:
```
NAME   FSTYPE FSVER LABEL UUID                                 FSAVAIL FSUSE% MOUNTPOINTS
sda                                                                           
├─sda1 ext4   1.0         10f956ce-83e9-46e2-ba25-1950b07cf97d  828,2G     1% /
├─sda2                                                                        
└─sda5 swap   1           174b12b9-7a64-4ed8-89ea-5ece1d59be0a                [SWAP]
sdb                                                                           
├─sdb1 ext4   1.0         39ac766f-480c-4cda-9282-0afdf06cd868            
├─sdb2                                                                        
└─sdb5 swap   1           cdb91357-a8f8-4f61-99db-4a8e3bd209e4  
```
El valor que ocupamos es el que esta al lado del nombre del disco, en este ejemplo el nombre es `sdb1` y el UUID es `39ac766f-480c-4cda-9282-0afdf06cd868`

4) Una vez tengamos el UUID ya podemos crear el mount requerido para el segundo disco. 
5) Hacemos la carpeta de docker con el siguiente comando `sudo mkdir /gabo && sudo mkdir /gabo/docker`.
6) Abrir el archivo `/etc/fstab`.
7) Agregar esta linea al final del archivo `UUID=PONER_UUID_AQUI   /gabo/docker   ext4   defaults   0   0`, remplazar `PONER_UUID_AQUI` con el UUID que sacamos de los pasos anteriores.
8) Guardar el archivo.
9) Correr el comando `sudo systemctl daemon-reload`.
10) Corriendo de nuevo `lsblk` nos damos cuenta que la ruta `/gabo/docker` ya fue montada:
```
NAME   MAJ:MIN RM   SIZE RO TYPE MOUNTPOINTS
sda      8:0    0 894,3G  0 disk 
└─sda1   8:1    0 894,3G  0 part /gabo/docker
sdb      8:16   0 894,3G  0 disk 
├─sdb1   8:17   0 893,3G  0 part /
├─sdb2   8:18   0     1K  0 part 
└─sdb5   8:21   0   975M  0 part [SWAP]
sr0     11:0    1  1024M  0 rom  
```
11) Crear el fichero de docker con este comando `sudo mkdir /etc/docker`
12) Luego crear el archivo `/etc/docker/daemon.json` y agregar este contenido:
```
{ 
   "data-root":"/gabo/docker" 
}
```
13) Reiniciar servidor y seguir con la guia de instalacion.