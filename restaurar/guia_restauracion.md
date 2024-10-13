# Guia para restaurar Gabo

1) Instalar Debian agregando al usuario gabo_admin
2) Correr esto en terminal: `wget https://github.com/davillafon12/gabos/raw/refs/heads/produccion/restaurar/instalar.sh -v -O instalar.sh && chmod 755 instalar.sh && ./instalar.sh;`.
3) Se va a abrir una ventana con el archivo de texto `.env`, aqui debe agregar todos los usuarios y contraseñas.
4) Descargar el ultimo respaldo de gabo de Dropbox, descomprimirlo, renombrarlo a `respaldo-gabo.sql` y colocarlo en la carpeta `gabos/restaurar/database/`.
5) Descargar el contenido de la carpeta de certificados de Dropbox (Documentos electronicos) y colocar los certificados en la carpeta `gabos/restaurar/app/certificados` (Si la carpeta no existe, crearla).
6) Descargar el contenido de la carpeta de imagenes de Dropbox y colocar las imagenes en la carpeta `gabos/restaurar/app/imagenes` (Si la carpeta no existe, crearla).
7) Correr el siguiente comando en la terminal `./instalar.sh`
8) Esperar unos 5 minutos a que todo corra y luego vayase a `localhost:8186/jasperserver` y acceda con el usuario y contraseña que ingreso en `.env`
9) Una vez dentro del servidor de jasper, vayase a `Administrar` -> `Ajustes del servidor` -> `Importar`, una vez ahi seleccione el archivo `export.zip` que esta en `gabos/restaurar/jasper`; luego seleccionar la opcion que dice `Clave Heredada` y por ultimo darle al boton de `Importar`. Con este paso se cargaran todos los reportes y configuracion al jasper report server.
10) Hay que actualizar la informacion de la base de datos en el jasper report server, para eso dele click al boton que es como una casita (En la parte de arriba-izquierda), en la barra del lado izquierdo dele click a `Data Sources` y se lo mostrara una que se llama `GaboProduccion`, dele click derecho y luego `Editar`. Una vez ahi ingrese los datos de la siguiente manera:
```
Anfitrión (obligatorio): gabo-database
Puerto (obligatorio): 3306
Base de datos (obligatorio): PONER AQUI EL NOMBRE DE LA BASE DE DATOS QUE SE USO EN .env (GABO_APP_DB_NAME)
URL (obligatorio): Dejar este campo como esta
Nombre de usuario: PONER AQUI EL USUARIO DE REPORTES QUE SE USO EN .env (JASPERREPORT_DB_CONSULTA_USER)
Contraseña: PONER AQUI LA CONTRASEÑA DE REPORTES QUE SE USO EN .env (JASPERREPORT_DB_CONSULTA_PASSWORD)
```
Una vez ingreso esos datos puede darle al boton `Guardar`, luego darle `Guardar` de nuevo.
11) Asegurarse que el usuario creado para rsync tenga membresia al grupo que trabaja con dropbox en la maquina host, si no rsync falla al sincronizar documentos electronicos

