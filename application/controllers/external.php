<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class external extends CI_Controller {	
    
    public $token = "Queladilla9876!!";
    
    function __construct(){
        parent::__construct(); 
        $this->load->model('factura','',TRUE);
        $this->load->model('empresa','',TRUE);
        $this->load->model('contabilidad','',TRUE);
        $this->load->model('cliente','',TRUE);
        $this->load->model('impresion_m','',TRUE);
    }

    public function actualizarComprobantes(){
        $empresas = array();
        require_once PATH_API_HACIENDA;
        $api = new API_FE();
        require_once PATH_API_CORREO;
        $apiCorreo = new Correo();
        echo "\n >>>>>>>>>>>>>".date(DATE_ATOM)." \n \n";
        echo "Revisando Facturas...\n";
        
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
                                    PATH_DOCUMENTOS_ELECTRONICOS.$factura->Clave.".xml",
                                    PATH_DOCUMENTOS_ELECTRONICOS.$factura->Clave."-respuesta.xml",
                                    PATH_DOCUMENTOS_ELECTRONICOS.$factura->Clave.".pdf");
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
    
    
	
} 

?>