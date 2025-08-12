<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class comprobantes extends CI_Controller {

    function __construct(){
            parent::__construct(); 
            $this->load->model('user','',TRUE);
            $this->load->model('cliente','',TRUE);
            $this->load->model('contabilidad','',TRUE);
            $this->load->model('empresa','',TRUE);
    }

    function index(){
        include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

        $permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

        if($permisos['aceptar_rechazar_comprobantes']){
            $data['javascript_cache_version'] = $this->javascriptCacheVersion;
            $this->load->view('contabilidad/aceptar_rechazar_comprobantes', $data);	
        }else{
           redirect('accesoDenegado', 'location');
        }
    }
    
    function cargarFacturas(){
        include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
        $data['javascript_cache_version'] = $this->javascriptCacheVersion;
        $data["comprobantes"] = array();
        $data["error"] = "No cargó los comprobantes";
                
        if(isset($_FILES["facturas"])){
            $facturas = $_FILES["facturas"];
            if(isset($facturas["name"])){
                if(sizeof($facturas["name"]) > 0){
                    $cantidadArchivos = sizeof($facturas["name"]);
                    if(trim($facturas["name"][0]) !== ""){
                        if($this->revisarFormatoFacturas($facturas)){
                            $facturasFinal = array();
                            $allWereProcessed = true;
                            for($counter = 0; $cantidadArchivos > $counter; $counter++){
                                try{
                                    $facturaXML = file_get_contents($facturas["tmp_name"][$counter]);
                                    $xml = simplexml_load_string($facturaXML);
                                    $clave = (String)$xml->Clave;
                                    $fechaEmision = (String)$xml->FechaEmision;
                                    $nombreEmisor = (String)$xml->Emisor->Nombre;
                                    $cedulaEmisor = (String)$xml->Emisor->Identificacion->Numero;
                                    $tipoCedulaEmisor = (String)$xml->Emisor->Identificacion->Tipo;
                                    $totalImpuestos = (String)$xml->ResumenFactura->TotalImpuesto;
                                    $totalFactura = (String)$xml->ResumenFactura->TotalComprobante;
                                    $receptorNombre = (String)$xml->Receptor->Nombre;
                                    $receptorCedula = (String)$xml->Receptor->Identificacion->Numero;
                                    
                                    array_push($facturasFinal, array("clave"=>$clave, 
                                                                     "nombreEmisor"=>$nombreEmisor,
                                                                     "cedulaEmisor"=>$cedulaEmisor,
                                                                     "tipoCedulaEmisor"=>$tipoCedulaEmisor,
                                                                     "fecha"=> date("d-m-Y H:i:s", strtotime($fechaEmision)),
                                                                     "fechaOriginal"=> $fechaEmision,
                                                                     "totalImpuestos"=>$totalImpuestos,
                                                                     "totalFactura"=>$totalFactura,
                                                                     "receptorNombre"=>$receptorNombre,
                                                                     "receptorCedula"=>$receptorCedula));
                                }catch(Exception $e){
                                    $allWereProcessed = false;
                                    $data["error"] = "Error al cargar el documento {$facturas["name"][$counter]}";
                                }
                            }
                            
                            if($allWereProcessed){
                                $data["comprobantes"] = $facturasFinal;
                                $_SESSION["comprobantes_tmp"] = $facturasFinal;
                                $data["tiposMensajes"] = $this->tipoMensajesMensajeReceptor;
                            }
                        }else{
                            $data["error"] = "Alguno de los comprobantes no está en formato XML";
                        }
                    }
                }
            }
        }
        
        $this->load->view('contabilidad/comprobantes_cargados', $data);
    }
    
    private function revisarFormatoFacturas($facturas){
        $formatosPermitidos = array("text/xml", "application/xml");
        $cantidadFacturas = sizeof($facturas["name"]);
        for($counter = 0; $cantidadFacturas > $counter; $counter++){
            if(isset($facturas["type"][$counter])){
                if(!in_array($facturas["type"][$counter], $formatosPermitidos)){
                    return false;
                }
            }
        }
        return true;
    }
    
    function procesar(){
        include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
        $comprobantes = $_SESSION["comprobantes_tmp"];
        $facturas = json_decode($_POST["facturas"], true);
         
        $r["status"] = false;
        $r["error"] = "No se pudo procesar la solicitud";
        
        if(sizeof($comprobantes) == sizeof($facturas)){
            if($this->revisarComprobantesCargadosIgualAProcesar($comprobantes, $facturas)){
                if($empresa = $this->empresa->getEmpresa($data['Sucursal_Codigo'])){
                    $empresa = $empresa[0];
                    if($this->revisarQueComprobantesSeanDelMismoReceptor($comprobantes, $empresa->Sucursal_Cedula)){
                        require_once PATH_API_HACIENDA;
                        $api = new API_FE();
                        if($api->internetIsOnline()){
                            $consecutivoInicial = $this->contabilidad->getSiguienteConsecutivoMensajeReceptor($empresa->Codigo);

                            for($counter = 0; $counter < sizeof($comprobantes); $counter++){
                                foreach($facturas as $f){
                                    if($f["clave"] == $comprobantes[$counter]["clave"]){
                                        $comprobantes[$counter]["tipoDocumento"] = $f["estado"];
                                    }
                                }
                            }

                            foreach($comprobantes as $c){
                                $this->contabilidad->agregarInfoBasicaMensajeReceptor($empresa->Codigo, 
                                                                                    $consecutivoInicial, 
                                                                                    $empresa->Tipo_Cedula, 
                                                                                    $c["receptorCedula"], 
                                                                                    $empresa->Codigo_Pais_Fax, 
                                                                                    "normal", 
                                                                                    rand(10000000,99999999), 
                                                                                    $c["tipoDocumento"], 
                                                                                    $c["clave"], 
                                                                                    $c["nombreEmisor"], 
                                                                                    $c["cedulaEmisor"],
                                                                                    $c["tipoCedulaEmisor"],
                                                                                    date(DATE_ATOM), 
                                                                                    $c["totalImpuestos"], 
                                                                                    $c["totalFactura"], 
                                                                                    $c["fechaOriginal"]);

                                if($this->contabilidad->generarClaveYConsecutivoMensajeReceptor($consecutivoInicial, $empresa->Codigo)){
                                    if($this->contabilidad->generarXMLMensajeReceptor($consecutivoInicial, $empresa->Codigo)){
                                        if($this->contabilidad->firmarXMLMensajeReceptor($consecutivoInicial, $empresa->Codigo)){
                                            if($this->contabilidad->enviarMensajeReceptorHacienda($consecutivoInicial, $empresa->Codigo)){
                                                
                                            }
                                        }
                                    }
                                }


                                $consecutivoInicial++; // Lo aumentamos para el siguiente comprobante
                            }
                            
                            unset($r["error"]);
                            $r["status"] = true;
                        }else{
                            $r["error"] = "No hay conexión a internet, por favor inténtelo mas tarde";
                        }
                    }else{
                        $r["error"] = "En algunos comprobantes la cédula del receptor no concuerda con la cédula de la sucursal actual, favor revisar factura puesto puede que se haya generado mal desde el emisor";
                    }
                }else{
                    $r["error"] = "No se pudo cargar la información de la sucursal";
                }
            }else{
                $r["error"] = "Los comprobantes cargados no concuerdan con los comprobantes a procesar";
            }
        }else{
            $r["error"] = "La cantidad de comprobantes cargados no concuerda con la cantidad de comprobantes a procesar";
        }
        
        echo json_encode($r);
    }
	
    private function revisarComprobantesCargadosIgualAProcesar($comprobantes, $facturas){
        foreach($comprobantes as $c){
            $siEsta = false;
            foreach($facturas as $f){
                if($f["clave"] == $c["clave"]){
                    $siEsta = true;
                }
            }
            
            if(!$siEsta){
                return false;
            }
        }
        return true;
    }
    
    private function revisarQueComprobantesSeanDelMismoReceptor($comprobantes, $cedulaReceptor){
        foreach($comprobantes as $c){
            if($c["receptorCedula"] !== $cedulaReceptor){
                return false;
            }
        }
        return true;
    }
}

?>