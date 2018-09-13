<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class external extends CI_Controller {	
    
    function __construct(){
        parent::__construct(); 
        $this->load->model('factura','',TRUE);
        $this->load->model('empresa','',TRUE);
        $this->load->model('contabilidad','',TRUE);
    }

    public function actualizarComprobantes(){
        $empresas = array();
        require_once PATH_API_HACIENDA;
        $api = new API_FE();
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
                    if($this->factura->getEstadoFacturaHacienda($api, $empresa, $factura, $tokenData, $factura->Consecutivo, $factura->Sucursal)){
                        echo "Se actualizo el estado de la factura #{$factura->Consecutivo} de la sucursal #{$factura->Sucursal} \n";
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
	
} 

?>