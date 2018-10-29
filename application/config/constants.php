<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/**
 * ---------------------------   WINDOWS PATHS  ----------------------------------
 */

//define('PATH_LOG_OUT_HEADER','/../Header/log_out_from_browser_Script.php');
//define('PATH_HEADER_PICTURE','/../Header/Header_Picture.php');
//define('PATH_HEADER_SELECTOR_MENU','/../Header/selector_menu.php');
//define('PATH_HEADER_LOG_IN_INFO','/../Header/Log_In_Information.php');
//define('PATH_FOOTER','/../Footer/Default_Footer.php');
//define('PATH_USER_DATA','/../get_session_data.php');
//define('PATH_USER_DATA_DOUBLE','/../controllers/get_session_data.php');
//define('PATH_FPDF_LIBRARY', '/../libraries/fpdf/fpdf.php');
//define('PATH_MENU_PRINCIPAL_VENDEDOR', '/../Header/Menu_Principal_Vendedor.php');
//define('PATH_MENU_PRINCIPAL_CAJERO', '/../Header/Menu_Principal_Cajero.php');
//define('PATH_MENU_PRINCIPAL','/../Header/Menu_Principal.php');
//define('PATH_PERMISOS', '/../../controllers/usuarios/permisos.php');
//define('PATH_DESACTIVAR_USUARIOS_SCRIPT', '/../../scripts/ajax_desactivar_usuarios.php');
//define('PATH_DESACTIVAR_CLIENTES_SCRIPT', '/../../scripts/ajax_desactivar_clientes.php');
//define('PATH_BUSCAR_CLIENTE_ID_SCRIPT', '/../../scripts/ajax_verify_cliente_id.php');
//define('PATH_API_HACIENDA', '/../libraries/API_Hacienda/API_FE.php');
//define('PATH_REST_CLIENT', '/../libraries/API_Hacienda/RestClient.php');
//define('PATH_API_LOGGER', '/../libraries/API_Hacienda/APILogger.php');
//define('PATH_TABLA_FAMILIAS', '/../../scripts/cargar_tabla_familias.php');
//define('PATH_AJAX_FAMILIAS', '/../../scripts/ajax_familias.php');
//define('PATH_AJAX_VERIFY_FAMILIAS', '/../../scripts/ajax_verify_familia_id.php');
//define('PATH_API_LOGGING', '/../../logs/API_FE');
//define('PATH_DOCUMENTOS_ELECTRONICOS', '/../../third_party/');
//define('PATH_API_CORREO', '/../libraries/Correo.php');
//define('CARPETA_IMAGENES', FCPATH.'application\\images\\articulos\\');

/**
 * ---------------------------    LINUX PATHS  ----------------------------------
 */

define('PATH_LOG_OUT_HEADER',FCPATH.'application/views/Header/log_out_from_browser_Script.php');
define('PATH_HEADER_PICTURE',FCPATH.'application/views/Header/Header_Picture.php');
define('PATH_HEADER_SELECTOR_MENU',FCPATH.'application/views/Header/selector_menu.php');
define('PATH_HEADER_LOG_IN_INFO',FCPATH.'application/views/Header/Log_In_Information.php');
define('PATH_FOOTER',FCPATH.'application/views/Footer/Default_Footer.php');
define('PATH_USER_DATA',FCPATH.'application/controllers/get_session_data.php');
define('PATH_USER_DATA_DOUBLE',FCPATH.'application/controllers/get_session_data.php');
define('PATH_FPDF_LIBRARY', FCPATH.'application/libraries/fpdf/fpdf.php');
define('PATH_MENU_PRINCIPAL_VENDEDOR', FCPATH.'application/views/Header/Menu_Principal_Vendedor.php');
define('PATH_MENU_PRINCIPAL_CAJERO', FCPATH.'application/views/Header/Menu_Principal_Cajero.php');
define('PATH_MENU_PRINCIPAL',FCPATH.'application/views/Header/Menu_Principal.php');
define('PATH_PERMISOS', FCPATH.'application/controllers/usuarios/permisos.php');
define('PATH_DESACTIVAR_USUARIOS_SCRIPT', FCPATH.'application/scripts/ajax_desactivar_usuarios.php');
define('PATH_DESACTIVAR_CLIENTES_SCRIPT', FCPATH.'application/scripts/ajax_desactivar_clientes.php');
define('PATH_BUSCAR_CLIENTE_ID_SCRIPT', FCPATH.'application/scripts/ajax_verify_cliente_id.php');
define('PATH_API_HACIENDA', FCPATH.'application/libraries/API_Hacienda/API_FE.php');
define('PATH_REST_CLIENT', FCPATH.'application/libraries/API_Hacienda/RestClient.php');
define('PATH_API_LOGGER', FCPATH.'application/libraries/API_Hacienda/APILogger.php');
define('PATH_TABLA_FAMILIAS', FCPATH.'application/scripts/cargar_tabla_familias.php');
define('PATH_AJAX_FAMILIAS', FCPATH.'application/scripts/ajax_familias.php');
define('PATH_AJAX_VERIFY_FAMILIAS', FCPATH.'application/scripts/ajax_verify_familia_id.php');
define('PATH_API_LOGGING', FCPATH.'application/logs/API_FE');
define('PATH_DOCUMENTOS_ELECTRONICOS', FCPATH.'application/third_party/');
define('PATH_API_CORREO', FCPATH.'application/libraries/Correo.php');
define('CARPETA_IMAGENES', FCPATH.'application/images/articulos/');


define('URL_API_CRLIBE', 'http://192.168.10.17');
define('API_CRLIBRE_CURL_TIMEOUT', 10);
define('CODIGO_PAIS', 506);
define('FACTURA_ELECTRONICA', 'FE');
define('FACTURA_ELECTRONICA_CODIGO', '01');
define('NOTA_CREDITO_ELECTRONICA', 'NC');
define('HACIENDA_DECIMALES', 5);
define('HACIENDA_TOKEN_API_STAG', "https://idp.comprobanteselectronicos.go.cr/auth/realms/rut-stag/protocol/openid-connect/token");
define('HACIENDA_TOKEN_API_PROD', "https://idp.comprobanteselectronicos.go.cr/auth/realms/rut/protocol/openid-connect/token");
define('HACIENDA_RECEPCION_API_STAG', "https://api.comprobanteselectronicos.go.cr/recepcion-sandbox/v1/");
define('HACIENDA_RECEPCION_API_PROD', "https://api.comprobanteselectronicos.go.cr/recepcion/v1/");
define('PATH_DOCUMENTOS_ELECTRONICOS_WEB', 'application/third_party/');

define('ANULAR_FACTURA', '01');
define('CORRIGE_FACTURA', '03');

/* End of file constants.php */
/* Location: ./application/config/constants.php */