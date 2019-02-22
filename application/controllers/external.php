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
        echo "\n >>>>>>>>>>>>>".date(DATE_ATOM)." \n \n";
        echo "Revisando Facturas...\n";
        $empresa = null;
        
        if($facturas = $this->factura->getFacturasRecibidasHacienda()){
            foreach($facturas as $factura){
                if(!isset($empresas[$factura->Sucursal])){
                    $empresas[$factura->Sucursal] = $this->empresa->getEmpresa($factura->Sucursal)[0];
                }
                $empresa = $empresas[$factura->Sucursal];
                echo "Obteniendo token... \n";
                if($tokenData = $api->solicitarToken($empresa->Ambiente_Tributa, $empresa->Usuario_Tributa, $empresa->Pass_Tributa)){
                    if($resultado = $this->factura->getEstadoFacturaHacienda($api, $empresa, $factura, $tokenData, $factura->Consecutivo, $factura->Sucursal)){
                        $estadoActualizado = $resultado["estado_hacienda"];
                        echo "Se actualizo el estado -$estadoActualizado- de la factura #{$factura->Consecutivo} de la sucursal #{$factura->Sucursal} \n";
                        
                        // Revisar si fue aceptado para enviar correo
                        if($resultado["status"] && $estadoActualizado === "aceptado"){
                            echo "Enviando correo \n";
                            if(filter_var($factura->ReceptorEmail, FILTER_VALIDATE_EMAIL)){
                                $attachs = array(
                                    $this->factura->getFinalPath("fe").$factura->Clave.".xml",
                                    $this->factura->getFinalPath("fe").$factura->Clave."-respuesta.xml",
                                    $this->factura->getFinalPath("fe").$factura->Clave.".pdf");
                                if($apiCorreo->enviarCorreo($factura->ReceptorEmail, "Factura Electrónica #".$factura->Consecutivo." | ".$empresa->Sucursal_Nombre, "Este mensaje se envió automáticamente a su correo al generar una factura electrónica bajo su nombre.", "Factura Electrónica - ".$empresa->Sucursal_Nombre, $attachs)){
                                    $this->factura->marcarEnvioCorreoFacturaElectronica($factura->Sucursal, $factura->Consecutivo);
                                    echo "Correo enviado con exito \n"; 
                                }else{
                                    echo "Error al enviar correo \n";
                                }
                            }else{
                                echo "Cliente no es receptor de correo \n";
                            }
                        }
                    }else{
                        echo "NO se actualizo el estado de la factura #{$factura->Consecutivo} de la sucursal #{$factura->Sucursal} \n";
                    }
                }else{
                    echo "Hubo un error al obtener el token para facturas \n";
                }
            }
            
        }else{
            echo "No hay facturas que procesar \n";
        }
        
        echo "Revisando Notas Credito... \n";
        
        if($notas = $this->contabilidad->getNotasCreditoRecibidasHacienda()){
            foreach($notas as $nota){
                if(!isset($empresas[$nota->Sucursal])){
                    $empresas[$nota->Sucursal] = $this->empresa->getEmpresa($nota->Sucursal)[0];
                }
                $empresa = $empresas[$nota->Sucursal];
                echo "Obteniendo token... \n";
                if($tokenData = $api->solicitarToken($empresa->Ambiente_Tributa, $empresa->Usuario_Tributa, $empresa->Pass_Tributa)){
                    if($this->contabilidad->getEstadoNotaCreditoHacienda($api, $nota, $empresa, $tokenData, $nota->Consecutivo, $nota->Sucursal)){
                        echo "Se actualizo el estado de la nota credito #{$nota->Consecutivo} de la sucursal #{$nota->Sucursal} \n";
                    }else{
                        echo "NO se actualizo el estado de la nota credito #{$nota->Consecutivo} de la sucursal #{$nota->Sucursal} \n";
                    }
                }else{
                    echo "Hubo un error al obtener el token para notas \n";
                }
            }
            
        }else{
            echo "No hay notas credito que procesar \n";
        }
        
        echo "Revisando Mensajes Receptores... \n";
        
        if($mensajes = $this->contabilidad->getMensajeReceptoresRecibidasHacienda()){
            foreach($mensajes as $mensaje){
                if(!isset($empresas[$mensaje->Sucursal])){
                    $empresas[$mensaje->Sucursal] = $this->empresa->getEmpresa($mensaje->Sucursal)[0];
                }
                $empresa = $empresas[$mensaje->Sucursal];
                echo "Obteniendo token... \n";
                if($tokenData = $api->solicitarToken($empresa->Ambiente_Tributa, $empresa->Usuario_Tributa, $empresa->Pass_Tributa)){
                    if($this->contabilidad->getEstadoMensajeReceptorHacienda($api, $empresa, $mensaje, $tokenData, $mensaje->Consecutivo, $mensaje->Sucursal)){
                        echo "Se actualizo el estado del mensaje receptor #{$mensaje->Consecutivo} de la sucursal #{$mensaje->Sucursal} \n";
                    }else{
                        echo "NO se actualizo el estado del mensaje receptor #{$mensaje->Consecutivo} de la sucursal #{$mensaje->Sucursal} \n";
                    }
                }else{
                    echo "Hubo un error al obtener el token para mensaje receptor \n";
                }
            }
            
        }else{
            echo "No hay mensajes receptor que procesar \n";
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
        include 'get_session_data.php';
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
                
                if($res["status"]){
                    $this->logger->info("enviarComprobantesAHacienda", " La factura {$factura->Consecutivo} de la sucursal {$factura->Sucursal} fue ACEPTADA");
                    if(filter_var($factura->ReceptorEmail, FILTER_VALIDATE_EMAIL)){
                        $this->logger->info("enviarComprobantesAHacienda", "Enviando correo a cliente");
                        require_once PATH_API_CORREO;
                        $apiCorreo = new Correo();
                        $attachs = array(
                            $this->factura->getFinalPath("fe").$resFacturaElectronica["data"]["clave"].".xml",
                            $this->factura->getFinalPath("fe").$resFacturaElectronica["data"]["clave"]."-respuesta.xml",
                            $this->factura->getFinalPath("fe").$resFacturaElectronica["data"]["clave"].".pdf");
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
                    if($res["status"] === false && $res["estado"] === "rechazado"){
                        $this->logger->info("enviarComprobantesAHacienda", "Generamos nota credito respectiva por factura rechazada");
                        // Al haber sido rechazada por hacienda se debe anular la factura, y se devuelven los articulos
                        //$this->devolverProductosdeFactura($responseCheck["factura"]->Factura_Consecutivo, $responseCheck["factura"]->TB_02_Sucursal_Codigo);
                        if($articulosFactura = $this->factura->getArticulosFactura($factura->Consecutivo, $factura->Sucursal)){
                            $this->logger->info("enviarComprobantesAHacienda", "Articulos Obtenidos");
                            // Creamos la nota credito respectiva para la factura
                            $productosAAcreditar = $this->convertirProductosDeFacturaANotaCredito($articulosFactura);
                            $facturaGabo = $this->factura->getFacturasHeaders($factura->Consecutivo, $factura->Sucursal);
                 
                            $retorno = array();
                            $this->logger->info("enviarComprobantesAHacienda", "Generando y enviando NC de anulacion");
                            
                            $this->contabilidad->crearNotaCreditoMacro($retorno, $facturaGabo[0]->TB_03_Cliente_Cliente_Cedula, $factura->Consecutivo, $factura->Consecutivo, $factura->Sucursal, $productosAAcreditar, $data['Usuario_Codigo'], ANULAR_FACTURA, "Anulacion por rechazo de factura", true);
                            $this->logger->info("enviarComprobantesAHacienda", "Resultado de la NC: ".json_encode($retorno));
                        }else{
                            $this->logger->error("enviarComprobantesAHacienda", "No se logro obtener los articulos para generar la NC");
                        }
                    }
                }
            }
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
        }else{
            $this->logger->info("enviarComprobantesAHacienda", "No hay facturas que enviar a Hacienda");
        }
        echo "FIN";
    }
    
	
} 

?>