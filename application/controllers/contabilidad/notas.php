<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class notas extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('cliente','',TRUE);
		$this->load->model('contabilidad','',TRUE);	
	}

	function index()
	{
		redirect('home', 'location');		
	}
	
	function notasCredito(){
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if($permisos['entrar_notas'])
		{
			$this->load->view('contabilidad/notas_credito_view', $data);			
		}
		else{
			redirect('accesoDenegado', 'location');
		}
	
	}
}

?>