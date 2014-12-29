<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class retiro extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('contabilidad','',TRUE);
	}

	function index()
	{
		redirect('home', 'location');		
	}
	
	function parcial(){
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if($permisos['crear_retiros'])
		{
			$this->load->view('contabilidad/retiros_parciales_view', $data);			
		}
		else{
			redirect('accesoDenegado', 'location');
		}	
	}
	
	function crearRetiro(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['cantidad'])){
			$cantidad = $_POST['cantidad'];
			$cantidad = str_replace(".","",$cantidad);
			$cantidad = str_replace(",",".",$cantidad);
			if(is_numeric($cantidad)){
				include '/../get_session_data.php';
				date_default_timezone_set("America/Costa_Rica");
				$fecha = date("y/m/d : H:i:s", now());
				//echo $cantidad;
				$this->contabilidad->crearRetiroParcial($cantidad, $fecha, $data['Usuario_Codigo'], $data['Sucursal_Codigo']);
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario realizo un retiro parcial de: $cantidad",$data['Sucursal_Codigo'],'retiro_parcial');
				$retorno['status'] = 'success';
				unset($retorno['error']);
			}else{
				$retorno['error'] = '3'; //Cantidad no valida
			}
		}else{
			$retorno['error'] = '2'; //URL mala
		}
		echo json_encode($retorno);	
	
	}
	
}

?>