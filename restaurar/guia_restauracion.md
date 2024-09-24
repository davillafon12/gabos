# Guia para restaurar Gabo

1) Instalar Debian
2) Instalar docker con docker-compose
3) Instalar dropbox (Requiere credenciales de dropbox)
4) Instalar rsync con este comando: `sudo apt install rsync -y`
4) Clonar el repositorio de gabos
5) Descargar el ultimo respaldo de la base de datos de gabo, certificados e imagenes de articulos.
6) Los respaldos de la base de datos van en `gabos/restaurar/database` y se deben renombar a `respaldo-gabo.sql`.
7) Los respaldos de certificados e imagenes van en `gabos/restaurar/app`. Los certificados deben de estar en `gabos/restaurar/app/certificados` y las imagenes deben de estar en `gabos/restaurar/app/imagenes`.
8) Irse a la carpeta de `gabos/restaurar/jasper` e importar la imagen del jasper reports usando el siguiente comando: `docker load -i gabo_jasper_reports_image.tar.gz`
9) Llenar el archivo `.env` con las cuentas y contraseñas requeridas (Este acrhivo NUNCA debe subirse al github ni ser usado fuera del servidor)
10) Vayase a la carpeta donde esta `gabos` y desde ahi ejecute lo siguiente: `docker compose up -d`
11) Esperar unos 5 minutos a que todo corra y luego vayase a `localhost:8186/jasperserver` y acceda con el usuario y contraseña que ingreso en `.env`
12) Una vez dentro del servidor de jasper, vayase a `Administrar` -> `Ajustes del servidor` -> `Importar`, una vez ahi seleccione el archivo `export.zip` que esta en `gabos/restaurar/jasper`; luego seleccionar la opcion que dice `Clave Heredada` y por ultimo darle al boton de `Importar`. Con este paso se cargaran todos los reportes y configuracion al jasper report server.
13) Hay que actualizar la informacion de la base de datos en el jasper report server, para eso dele click al boton que es como una casita (En la parte de arriba-izquierda), en la barra del lado izquierdo dele click a `Data Sources` y se lo mostrara una que se llama `GaboProduccion`, dele click derecho y luego `Editar`. Una vez ahi ingrese los datos de la siguiente manera:
```
Anfitrión (obligatorio): gabo-database
Puerto (obligatorio): 3306
Base de datos (obligatorio): PONER AQUI EL NOMBRE DE LA BASE DE DATOS QUE SE USO EN .env (GABO_APP_DB_NAME)
URL (obligatorio): Dejar este campo como esta
Nombre de usuario: PONER AQUI EL USUARIO DE REPORTES QUE SE USO EN .env (JASPERREPORT_DB_CONSULTA_USER)
Contraseña: PONER AQUI LA CONTRASEÑA DE REPORTES QUE SE USO EN .env (JASPERREPORT_DB_CONSULTA_PASSWORD)
```
Una vez ingreso esos datos puede darle al boton `Guardar`, luego darle `Guardar` de nuevo.
14) Asegurarse que el usuario creado para rsync tenga membresia al grupo que trabaja con dropbox en la maquina host, si no rsync falla al sincronizar documentos electronicos