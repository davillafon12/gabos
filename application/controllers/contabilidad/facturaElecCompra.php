<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class facturaElecCompra extends CI_Controller {

    function __construct(){
            parent::__construct(); 
            $this->load->model('user','',TRUE);
            $this->load->model('cliente','',TRUE);
            $this->load->model('contabilidad','',TRUE);
            $this->load->model('empresa','',TRUE);
            $this->load->model('ubicacion','',TRUE);
    }
    
    function index(){
        include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

        $permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

        if($permisos['crear_factura_elec_compra']){
            $data['javascript_cache_version'] = $this->javascriptCacheVersion;
            $data['provincias'] = $this->ubicacion->getProvincias();
            $data['condicionesventa'] = $this->condicionesdeventa;
            $data['tiposdepago'] = $this->tiposdepago;
            $this->load->view('contabilidad/crear_factura_elec_compra_view', $data);	
        }else{
           redirect('accesoDenegado', 'location');
        }
    }
}