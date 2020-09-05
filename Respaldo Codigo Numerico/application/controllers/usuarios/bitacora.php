<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class bitacora extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);
	}

	function index()
	{
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion

		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if($permisos['ver_bitacora'])
		{
			$transacciones = $this->user->getTransacciones();
			$data['transacciones'] = $transacciones;
			$this->load->view('usuarios/usuarios_bitacora_view', $data);	
		}
		else{
			redirect('accesoDenegado', 'location');
		}
	}

}

?>