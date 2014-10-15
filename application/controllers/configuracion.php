<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class configuracion extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		include 'get_session_data.php'; //Esto es para traer la informacion de la sesion
		$this->load->model('user','',TRUE);	
		$this->load->model('XMLParser','',TRUE);
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['entrar_configuracion'])
		{	
			redirect('accesoDenegado', 'location');	
		}
	}

	function index()
	{
		include 'get_session_data.php';
		//Traemos el array con toda la info
		$conf_array = $this->XMLParser->getConfigArray();
		$data['c_array'] = $conf_array;
		$this->load->helper(array('form'));
		//print_r($conf_array);
		$this->load->view('view_configuracion', $data);	
	}
	
	function guardar()
	{	    
		$venta_min = $this->input->post('venta_min');
		$compra_min = $this->input->post('compra_min');
		$venta_dolar = $this->input->post('venta_dolar');
		$compra_dolar = $this->input->post('compra_dolar');
		$cant_dec = $this->input->post('cant_dec');
		$email = $this->input->post('email');
		$cant_ses = $this->input->post('cant_ses');
		$iva_cant = $this->input->post('iva_cant');
		//echo $email; 
		$result = $this->XMLParser->saveXML($venta_min, $compra_min, $venta_dolar, $compra_dolar, $cant_dec, $email, $cant_ses, $iva_cant);		
		include 'get_session_data.php'; //Esto es para traer la informacion de la sesion
		$ruta_base_imagenes_script = base_url('application/images/scripts');
		if($result)
		{
			$data['post_message']="<div class='status_2'><img src=".$ruta_base_imagenes_script."/tick.gif /><p class='text_status'>¡Se guardo correctamente!</div></p>";
		}
		else
		{
			$data['post_message']="<div class='status_2'><img src=".$ruta_base_imagenes_script."/error.gif /><p class='text_status'>¡No se guardo correctamente!</p></div>";
		}
		$conf_array = $this->XMLParser->getConfigArray();
		$data['c_array'] = $conf_array;
		$this->load->helper(array('form'));
		$this->load->view('view_configuracion', $data);
	}
 
}// FIN DE LA CLASE


?>