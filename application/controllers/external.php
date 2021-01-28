<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class external extends CI_Controller {

    private $token = "Queladilla9876!!";
    private $logger;

    function __construct(){
        parent::__construct();
        $this->load->model('factura','',TRUE);
        $this->load->model('empresa','',TRUE);
        $this->load->model('contabilidad','',TRUE);
        $this->load->model('cliente','',TRUE);
        $this->load->model('impresion_m','',TRUE);
        $this->load->model('articulo','',TRUE);
        $this->load->model('user','',TRUE);
        require_once PATH_API_LOGGER;
        $this->logger = new APILogger(PATH_UTILS_LOGGING);
    }

    public function actualizarComprobantes(){
        $empresas = array();
        require_once PATH_API_HACIENDA;
        $api = new API_FE();
        require_once PATH_API_CORREO;
        $apiCorreo = new Correo();
        echo "<br> >>>>>>>>>>>>>".date(DATE_ATOM)." <br> <br>";
        echo "Revisando Facturas...<br>";
        $empresa = null;

        if($facturas = $this->factura->getFacturasRecibidasHacienda()){
            foreach($facturas as $factura){
                if(!isset($empresas[$factura->Sucursal])){
                    $empresas[$factura->Sucursal] = $this->empresa->getEmpresa($factura->Sucursal)[0];
                }
                $empresa = $empresas[$factura->Sucursal];
                echo "Obteniendo token... <br>";
                if($tokenData = $api->solicitarToken($empresa->Ambiente_Tributa, $empresa->Usuario_Tributa, $empresa->Pass_Tributa)){
                    if($resultado = $this->factura->getEstadoFacturaHacienda($api, $empresa, $factura, $tokenData, $factura->Consecutivo, $factura->Sucursal)){
                        $estadoActualizado = $resultado["estado_hacienda"];
                        echo "Se actualizo el estado -$estadoActualizado- de la factura #{$factura->Consecutivo} de la sucursal #{$factura->Sucursal} <br>";

                        // Revisar si fue aceptado para enviar correo
                        if($resultado["status"] && $estadoActualizado === "aceptado"){
                            echo "Enviando correo <br>";
                            if(filter_var($factura->ReceptorEmail, FILTER_VALIDATE_EMAIL)){
                                $checkAttachs = array(
                                    "xml" => $this->factura->getFinalPath("fe", $factura->FechaEmision).$factura->Clave.".xml",
                                    "respuesta" => $this->factura->getFinalPath("fe", $factura->FechaEmision).$factura->Clave."-respuesta.xml",
                                    "pdf" => $this->factura->getFinalPath("fe", $factura->FechaEmision).$factura->Clave.".pdf");
                                $this->factura->checkArchivosCorreoFE($checkAttachs, $factura);
                                $attachs = array(
                                    $this->factura->getFinalPath("fe", $factura->FechaEmision).$factura->Clave.".xml",
                                    $this->factura->getFinalPath("fe", $factura->FechaEmision).$factura->Clave."-respuesta.xml",
                                    $this->factura->getFinalPath("fe", $factura->FechaEmision).$factura->Clave.".pdf");
                                if($apiCorreo->enviarCorreo($factura->ReceptorEmail, "Factura Electrónica #".$factura->Consecutivo." | ".$empresa->Sucursal_Nombre, "Este mensaje se envió automáticamente a su correo al generar una factura electrónica bajo su nombre.", "Factura Electrónica - ".$empresa->Sucursal_Nombre, $attachs)){
                                    $this->factura->marcarEnvioCorreoFacturaElectronica($factura->Sucursal, $factura->Consecutivo);
                                    echo "Correo enviado con exito <br>";
                                }else{
                                    echo "Error al enviar correo <br>";
                                }
                            }else{
                                echo "Cliente no es receptor de correo <br>";
                            }
                        }
                    }else{
                        echo "NO se actualizo el estado de la factura #{$factura->Consecutivo} de la sucursal #{$factura->Sucursal} <br>";
                    }
                }else{
                    echo "Hubo un error al obtener el token para facturas <br>";
                }
            }

        }else{
            echo "No hay facturas que procesar <br>";
        }

        echo "Revisando Notas Credito... <br>";

        if($notas = $this->contabilidad->getNotasCreditoRecibidasHacienda()){
            foreach($notas as $nota){
                if(!isset($empresas[$nota->Sucursal])){
                    $empresas[$nota->Sucursal] = $this->empresa->getEmpresa($nota->Sucursal)[0];
                }
                $empresa = $empresas[$nota->Sucursal];
                echo "Obteniendo token... <br>";
                if($tokenData = $api->solicitarToken($empresa->Ambiente_Tributa, $empresa->Usuario_Tributa, $empresa->Pass_Tributa)){
                    if($resEnvio = $this->contabilidad->getEstadoNotaCreditoHacienda($api, $nota, $empresa, $tokenData, $nota->Consecutivo, $nota->Sucursal)){
                        echo "Se actualizo al estado {$resEnvio["estado_hacienda"]} en la nota credito #{$nota->Consecutivo} de la sucursal #{$nota->Sucursal} <br>";

                        if($resEnvio){
                            if($resEnvio["estado_hacienda"] == "aceptado"){
                                if(filter_var($nota->ReceptorEmail, FILTER_VALIDATE_EMAIL)){
                                    require_once PATH_API_CORREO;
                                    $apiCorreo = new Correo();
                                    $attachs = array(
                                        $this->contabilidad->getFinalPath("nc", $nota->FechaEmision).$nota->Clave.".xml",
                                        $this->contabilidad->getFinalPath("nc", $nota->FechaEmision).$nota->Clave.".pdf",
                                        $this->contabilidad->getFinalPath("nc", $nota->FechaEmision).$nota->Clave."-respuesta.xml",);
                                    if($apiCorreo->enviarCorreo($nota->ReceptorEmail, "Nota Crédito #{$nota->Consecutivo} | ".$empresa->Sucursal_Nombre, "Este mensaje se envió automáticamente a su correo al generar una nota crédito bajo su nombre.", "Nota Crédito Electrónica - ".$empresa->Sucursal_Nombre, $attachs)){
                                        $this->contabilidad->marcarEnvioCorreoNotaCreditoElectronica($nota->Sucursal, $nota->Consecutivo);
                                    }
                                }
                            }
                        }


                    }else{
                        echo "NO se actualizo el estado de la nota credito #{$nota->Consecutivo} de la sucursal #{$nota->Sucursal} <br>";
                    }
                }else{
                    echo "Hubo un error al obtener el token para notas <br>";
                }
            }

        }else{
            echo "No hay notas credito que procesar <br>";
        }

        echo "Revisando Mensajes Receptores... <br>";

        if($mensajes = $this->contabilidad->getMensajeReceptoresRecibidasHacienda()){
            foreach($mensajes as $mensaje){
                if(!isset($empresas[$mensaje->Sucursal])){
                    $empresas[$mensaje->Sucursal] = $this->empresa->getEmpresa($mensaje->Sucursal)[0];
                }
                $empresa = $empresas[$mensaje->Sucursal];
                echo "Obteniendo token... <br>";
                if($tokenData = $api->solicitarToken($empresa->Ambiente_Tributa, $empresa->Usuario_Tributa, $empresa->Pass_Tributa)){
                    if($this->contabilidad->getEstadoMensajeReceptorHacienda($api, $empresa, $mensaje, $tokenData, $mensaje->Consecutivo, $mensaje->Sucursal)){
                        echo "Se actualizo el estado del mensaje receptor #{$mensaje->Consecutivo} de la sucursal #{$mensaje->Sucursal} <br>";
                    }else{
                        echo "NO se actualizo el estado del mensaje receptor #{$mensaje->Consecutivo} de la sucursal #{$mensaje->Sucursal} <br>";
                    }
                }else{
                    echo "Hubo un error al obtener el token para mensaje receptor <br>";
                }
            }

        }else{
            echo "No hay mensajes receptor que procesar <br>";
        }

        if($empresa !== null){
            $api->destruirSesion($empresa->Ambiente_Tributa, $empresa->Usuario_Tributa);
        }
    }

    public function regenerarXMLNotaCredito(){
        $consecutivo = trim(@$_GET["c"]);
        $sucursal = trim(@$_GET["s"]);
        $token = trim(@$_GET["t"]);

        if($token == $this->token){
            if($resXML = $this->contabilidad->generarXMLNotaCredito($consecutivo, $sucursal)){
                header('Content-Disposition: attachment; filename="xml.txt"');
                header('Content-Type: text/plain'); # Don't use application/force-download - it's not a real MIME type, and the Content-Disposition header is sufficient
                header('Content-Length: ' . strlen($resXML["XMLSinFirmar"]));
                header('Connection: close');
                echo $resXML["XMLSinFirmar"];
            }else{
                die("Error NC");
            }
        }else{
            die("Token");
        }

    }

    public function firmarXMLNotaCredito(){
        $consecutivo = trim(@$_GET["c"]);
        $sucursal = trim(@$_GET["s"]);
        $token = trim(@$_GET["t"]);

        if($token == $this->token){
            if($resXML = $this->contabilidad->firmarXMLNotaCredito($consecutivo, $sucursal)){
                header('Content-Disposition: attachment; filename="xmlFirmado.txt"');
                header('Content-Type: text/plain'); # Don't use application/force-download - it's not a real MIME type, and the Content-Disposition header is sufficient
                header('Content-Length: ' . strlen($resXML["XMLFirmado"]));
                header('Connection: close');
                echo $resXML["XMLFirmado"];
            }else{
                die("Error NC");
            }
        }else{
            die("Token");
        }

    }

    public function generarPDFNotaCredito(){
        $consecutivo = trim(@$_GET["c"]);
        $sucursal = trim(@$_GET["s"]);
        $token = trim(@$_GET["t"]);

        if($token == $this->token){
            $this->contabilidad->generarPDFNotaCredito($consecutivo, $sucursal);
            die("Generado PDF");
        }else{
            die("Token");
        }

    }

    public function anularFacturaSola(){
        $consecutivo = trim(@$_GET["c"]);
        $token = trim(@$_GET["t"]);

        if($token == $this->token){
            $tipoPago['tipo'] = "contado";
            if($responseCheck = $this->factura->validarCobrarFactura($consecutivo, $tipoPago)){
                include 'get_session_data.php';
                $productosAAcreditar = $this->convertirProductosDeFacturaANotaCredito($responseCheck["articulosOriginales"]);
                $retorno = array();
                $this->contabilidad->crearNotaCreditoMacro($retorno, $responseCheck["cliente"]->Cliente_Cedula, $responseCheck["factura"]->Factura_Consecutivo, $responseCheck["factura"]->Factura_Consecutivo, $responseCheck["factura"]->TB_02_Sucursal_Codigo, $productosAAcreditar, $data['Usuario_Codigo'], ANULAR_FACTURA, "Anulación autorizada por medio de caja o consulta", true);
                $this->contabilidad->generarPDFNotaCredito($retorno['nota'], $responseCheck["factura"]->TB_02_Sucursal_Codigo);
                die("Generada NC");
            }else{
                die("Fallo traer factura");
            }
        }else{
            die("Token");
        }

    }


    public function enviarComprobantesAHacienda(){
        $this->logger->info("enviarComprobantesAHacienda", ">>>>>>");
        $this->logger->info("enviarComprobantesAHacienda", ">>>>>> Comenzando envio por lote de facturas");
        $this->logger->info("enviarComprobantesAHacienda", ">>>>>>");
        $empresas = array();

        if($facturas = $this->factura->getFacturasSinEnviarAHacienda()){
            foreach($facturas as $factura){
                if(!isset($empresas[$factura->Sucursal])){
                    $empresas[$factura->Sucursal] = $this->empresa->getEmpresa($factura->Sucursal)[0];
                }
                $empresa = $empresas[$factura->Sucursal];
                $resFacturaElectronica = array("data" => array("situacion" => "normal", "clave" => $factura->Clave));
                $facturaObj = (Object) array("Factura_Consecutivo" => $factura->Consecutivo, "TB_02_Sucursal_Codigo" => $factura->Sucursal);
                $responseCheck["factura"] = $facturaObj;
                $responseCheck["empresa"] = $empresa;

                $this->logger->info("enviarComprobantesAHacienda", " Enviando la factura {$factura->Consecutivo} de la sucursal {$factura->Sucursal}");

                $res = $this->factura->envioHacienda($resFacturaElectronica, $responseCheck);

                /*if($res["status"]){
                    $this->logger->info("enviarComprobantesAHacienda", " La factura {$factura->Consecutivo} de la sucursal {$factura->Sucursal} fue ACEPTADA");
                    if(filter_var($factura->ReceptorEmail, FILTER_VALIDATE_EMAIL)){
                        $this->logger->info("enviarComprobantesAHacienda", "Enviando correo a cliente");
                        require_once PATH_API_CORREO;
                        $apiCorreo = new Correo();
                        $attachs = array(
                            $this->factura->getFinalPath("fe", $factura->FechaEmision).$resFacturaElectronica["data"]["clave"].".xml",
                            $this->factura->getFinalPath("fe", $factura->FechaEmision).$resFacturaElectronica["data"]["clave"]."-respuesta.xml",
                            $this->factura->getFinalPath("fe", $factura->FechaEmision).$resFacturaElectronica["data"]["clave"].".pdf");
                        if($apiCorreo->enviarCorreo(filter_var($factura->ReceptorEmail, FILTER_VALIDATE_EMAIL), "Factura Electrónica #".$responseCheck["factura"]->Factura_Consecutivo." | ".$responseCheck["empresa"]->Sucursal_Nombre, "Este mensaje se envió automáticamente a su correo al generar una factura electrónica bajo su nombre.", "Factura Electrónica - ".$responseCheck["empresa"]->Sucursal_Nombre, $attachs)){
                            $this->factura->marcarEnvioCorreoFacturaElectronica($responseCheck["factura"]->TB_02_Sucursal_Codigo, $responseCheck["factura"]->Factura_Consecutivo);
                            $this->logger->info("enviarComprobantesAHacienda", "Se envio correo con exito");
                        }else{
                            $this->logger->error("enviarComprobantesAHacienda", "Correo no se pudo enviar al cliente");
                        }
                    }else{
                        $this->logger->info("enviarComprobantesAHacienda", "Cliente no requiere envio de correo");
                    }
                }else{
                    $this->logger->error("enviarComprobantesAHacienda", " La factura {$factura->Consecutivo} de la sucursal {$factura->Sucursal} no fue enviada por:");
                    $this->logger->error("enviarComprobantesAHacienda", $res["message"]);
                }*/
            }
        }else{
            $this->logger->info("enviarComprobantesAHacienda", "No hay facturas que enviar a Hacienda");
        }








        $this->logger->info("enviarComprobantesAHacienda", ">>>>>>");
        $this->logger->info("enviarComprobantesAHacienda", ">>>>>> Comenzando envio por lote de notas credito");
        $this->logger->info("enviarComprobantesAHacienda", ">>>>>>");

        if($notas = $this->contabilidad->getNotasCreditoSinEnviarAHacienda()){
            foreach($notas as $nota){
                if(!isset($empresas[$nota->Sucursal])){
                    $empresas[$nota->Sucursal] = $this->empresa->getEmpresa($nota->Sucursal)[0];
                }
                $empresa = $empresas[$nota->Sucursal];

                $this->logger->info("enviarComprobantesAHacienda", " Enviando la nota credito {$nota->Consecutivo} de la sucursal {$nota->Sucursal}");

                $resEnvio = $this->contabilidad->enviarNotaCreditoElectronicaAHacienda($nota->Consecutivo, $nota->Sucursal);

                /*if($resEnvio){
                    if($resEnvio["estado_hacienda"] == "rechazado"){
                        $this->logger->error("enviarComprobantesAHacienda", "Nota credito fue RECHAZADA por Hacienda. | Consecutivo: {$nota->Consecutivo} | Sucursal: {$nota->Sucursal}");
                    }else if($resEnvio["estado_hacienda"] == "aceptado"){
                        $this->logger->info("enviarComprobantesAHacienda", "Nota credito fue ACEPTADA por Hacienda | Consecutivo: {$nota->Consecutivo} | Sucursal: {$nota->Sucursal}");
                        if(filter_var($nota->ReceptorEmail, FILTER_VALIDATE_EMAIL)){
                            $this->logger->info("enviarComprobantesAHacienda", "Enviando correo a cliente");
                            require_once PATH_API_CORREO;
                            $apiCorreo = new Correo();
                            $attachs = array(
                                $this->contabilidad->getFinalPath("nc", $nota->FechaEmision).$nota->Clave.".xml",
                                $this->contabilidad->getFinalPath("nc", $nota->FechaEmision).$nota->Clave.".pdf",
                                $this->contabilidad->getFinalPath("nc", $nota->FechaEmision).$nota->Clave."-respuesta.xml");
                            if($apiCorreo->enviarCorreo($nota->ReceptorEmail, "Nota Crédito #{$nota->Consecutivo} | ".$empresa->Sucursal_Nombre, "Este mensaje se envió automáticamente a su correo al generar una nota crédito bajo su nombre.", "Nota Crédito Electrónica - ".$empresa->Sucursal_Nombre, $attachs)){
                                $this->contabilidad->marcarEnvioCorreoNotaCreditoElectronica($nota->Sucursal, $nota->Consecutivo);
                                $this->logger->info("enviarComprobantesAHacienda", "Se envio correo con exito");
                            }else{
                                $this->logger->error("enviarComprobantesAHacienda", "Correo no se pudo enviar al cliente");
                            }
                        }else{
                            $this->logger->info("enviarComprobantesAHacienda", "Cliente no requiere envio de correo");
                        }
                    }else{
                        $this->logger->error("enviarComprobantesAHacienda", "Hacienda envio otro estado {$resEnvio["estado_hacienda"]} | Consecutivo: {$nota->Consecutivo} | Sucursal: {$nota->Sucursal}");
                    }
                }else{
                    $this->logger->error("enviarComprobantesAHacienda", "No se pudo enviar la nota credito a Hacienda, debemos marcarla como contingencia | Consecutivo: {$nota->Consecutivo} | Sucursal: {$nota->Sucursal}");
                }*/

            }
        }else{
            $this->logger->info("enviarComprobantesAHacienda", "No hay notas credito que enviar a Hacienda");
        }


        $this->logger->info("enviarComprobantesAHacienda", ">>>>>>");
        $this->logger->info("enviarComprobantesAHacienda", ">>>>>> Comenzando envio por lote de mensajes receptores");
        $this->logger->info("enviarComprobantesAHacienda", ">>>>>>");

        if($mensajesReceptores = $this->contabilidad->getMensajeReceptoresParaEnviarAHacienda()){
            foreach($mensajesReceptores as $mensajeReceptor){
                if(!isset($empresas[$mensajeReceptor->Sucursal])){
                    $empresas[$mensajeReceptor->Sucursal] = $this->empresa->getEmpresa($mensajeReceptor->Sucursal)[0];
                }
                $empresa = $empresas[$mensajeReceptor->Sucursal];

                $this->logger->info("enviarComprobantesAHacienda", " Enviando el mensaje receptor {$mensajeReceptor->Consecutivo} de la sucursal {$mensajeReceptor->Sucursal}");

                $respuesta = $this->contabilidad->enviarMensajeReceptorHacienda($mensajeReceptor->Consecutivo, $mensajeReceptor->Sucursal);

                if($respuesta){
                    if(isset($respuesta["status"])){
                        $this->logger->info("enviarComprobantesAHacienda", "Se envió mensaje receptor con estado <{$respuesta["estado_hacienda"]}>");
                    }
                }else{
                    $this->logger->info("enviarComprobantesAHacienda", "No se pudo enviar mensaje receptor");
                }
            }
        }else{
            $this->logger->info("enviarComprobantesAHacienda", "No hay mensajes receptores que enviar a Hacienda");
        }




        // Al final destruimos todas las sesiones con el API de Hacienda
        $this->logger->info("enviarComprobantesAHacienda", ">>>>>>");
        $this->logger->info("enviarComprobantesAHacienda", "Saliendo de sesiones de token:");
        require_once PATH_API_HACIENDA;
        $api = new API_FE();
        foreach($empresas as $empresa){
            $this->logger->info("enviarComprobantesAHacienda", "Destruyendo token de {$empresa->Usuario_Tributa}");
            $api->destruirSesion($empresa->Ambiente_Tributa, $empresa->Usuario_Tributa);
        }
        $this->logger->info("enviarComprobantesAHacienda", "Sesiones terminadas");
        $this->logger->info("enviarComprobantesAHacienda", ">>>>>>");

        echo "FIN";
    }

    public function testTLS(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.howsmyssl.com/a/check");
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $tlsVer = json_decode($response, true);
        echo "<pre>"; print_r($tlsVer);
        //echo "<h1>Your TSL version is: <u>" . ( $tlsVer['tls_version'] ? $tlsVer['tls_version'] : 'no TLS support' ) . "</u></h1>";
    }


    public function fixFacturaPelada(){
        $consecutivoString = trim(@$_GET["c"]);
        $sucursal = trim(@$_GET["s"]);
        $token = trim(@$_GET["t"]);


        if($token == $this->token){
            if($consecutivoString != ""){
                $consecutivos = explode(",", $consecutivoString);
                foreach($consecutivos as $consecutivo){
                    if(is_numeric($consecutivo)){
                        if($this->factura->existeFactura($consecutivo, $sucursal)){
                            if($this->factura->regenerarFacturaElectronicaPorContingencia($consecutivo, $sucursal)){
                                echo "Se regenero factura con exito $consecutivo <br>";
                            }else{
                                echo "No se pudo regenerar factura $consecutivo <br>";
                            }
                        }else{
                            echo "No existe factura para $consecutivo <br>";
                        }
                    }else{
                        echo "Consecutivo no valido $consecutivo <br>";
                    }
                }
            }else{
                die("Consecutivo vacio");
            }
        }else{
            die("You are not allowed to access here");
        }
    }

    public function actualizarMasivoCabys(){
        //die;
        $token = trim(@$_GET["t"]);
        $sucursal = trim(@$_GET["s"]);


        if($token == $this->token){
            if($this->empresa->getEmpresa($sucursal)){
                require_once './application/libraries/PHPExcel/IOFactory.php';
                $objPHPExcel = PHPExcel_IOFactory::load(FCPATH.'application/upload/carga_cabys.xls');
                $cantidadHojas = 1; //Para que solo procese la primera hoja del excel
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow();

                    //Empezamos en la fila 2 donde estan los datos
                    for ($fila = 2; $fila <= $highestRow; ++ $fila){
                        $codigo = $worksheet->getCellByColumnAndRow(0, $fila)->getValue();
                        $cabys = $worksheet->getCellByColumnAndRow(3, $fila)->getValue();

                        if($articulo = $this->articulo->existe_Articulo($codigo,$sucursal)){
                            $this->articulo->actualizarCodigoCabys($codigo, $sucursal, $cabys);
                            echo "Articulo $codigo se actualizo con codigo cabys $cabys <br>";
                        }else{
                            echo "Articulo $codigo no existe para la sucursal $sucursal <br>";
                        }

                    }

                    break; //Solo vamos a bretear la primera hoja
                }
            }else{
                die("Sucursal ingresada no es valida");
            }
        }else{
            die("You are not allowed to access here");
        }
    }

    public function actualizarPrecios(){
        if($empresas = $this->empresa->getEmpresas()){
            foreach($empresas as $empresa){
                echo "Actualizando precios de la empresa ".$empresa->Codigo." | ".$empresa->Sucursal_Nombre."<br>";
                if($articulos = $this->articulo->obtenerArticulosParaTablaFiltrados("", "", "", "", "", $empresa->Codigo)){
                    foreach($articulos->result() as $articulo){
                        $precio2 = $this->articulo->getPrecioProducto($articulo->codigo, 2, $empresa->Codigo);
                        $precio4 = $this->articulo->getPrecioProducto($articulo->codigo, 4, $empresa->Codigo);

                        echo "Actualizando precio del articulo ".$articulo->codigo." | Precio 2 = $precio2 | Precio 4 = $precio4 <br>";
                        log_message('error', "CAMBIO_PRECIO|".$empresa->Codigo."|".$articulo->codigo."|$precio2|$precio4");
                        $this->articulo->actualizarPrecio($articulo->codigo, $empresa->Codigo, $precio2, 4);
                    }
                }else{
                    echo "NO HAY ARTICULOS PARA EMPRESA ".$empresa->Codigo." | ".$empresa->Sucursal_Nombre."<br>";
                }
            }
        }else{
            die("No hay empresas");
        }
    }

}

?>