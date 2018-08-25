<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class registrar extends CI_Controller {

 function __construct()
 {
    parent::__construct(); 
	$this->load->model('user','',TRUE);
	$this->load->model('banco','',TRUE);
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if(!$permisos['registrar_banco'])
	{	
	   redirect('accesoDenegado', 'location');
	}
 }

 function index()
 {
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
	
	$this->load->helper(array('form'));
	$this->load->view('bancos/registrar_view_banco', $data);
 }
 
 function registraBanco()
 {
	$nombre_banco = $this->input->post('name');
	$comision_banco = $this->input->post('comision');
	
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
	
	//$id_empresa= $this->empresa->getCantidadEmpresas();
	
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	if($id = $this->banco->registrar($nombre_banco, $comision_banco, $data['Usuario_Codigo']))
	{ //Si se ingreso bien a la BD
		$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ingreso el banco ".mysql_real_escape_string($nombre_banco)." codigo: ".$id,$data['Sucursal_Codigo'],'registro');
		//Titulo de la pagina
		redirect('bancos/editar', 'location');
	}
	else
	{ }
	
	
 }
 
 
 
 


}

?>