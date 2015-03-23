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
			$data['tipo_cambio'] = $this->configuracion->getTipoCambioCompraDolar();
			$this->load->view('contabilidad/retiros_parciales_view', $data);			
		}
		else{
			redirect('accesoDenegado', 'location');
		}	
	}
	
	function crearRetiro(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['cantidad'])&&isset($_POST['tipo_cambio'])&&isset($_POST['colones'])&&isset($_POST['dolares'])){
			$cantidad = $_POST['cantidad'];
			$cantidad = str_replace(".","",$cantidad);
			$cantidad = str_replace(",",".",$cantidad);
			$tipo_cambio = $_POST['tipo_cambio'];
			if(is_numeric($cantidad)&&is_numeric($tipo_cambio)){
				$colones = json_decode($_POST['colones']);
				$dolares = json_decode($_POST['dolares']);
				include '/../get_session_data.php';
				date_default_timezone_set("America/Costa_Rica");
				$fecha = date("y/m/d : H:i:s", now());				
				//echo $cantidad;
				$retiro = $this->contabilidad->crearRetiroParcial($cantidad, $fecha, $tipo_cambio, $data['Usuario_Codigo'], $data['Sucursal_Codigo']);
				
				foreach($colones as $colon){
					$tipo = 'moneda';
					if($colon->denominacion>500){
						$tipo = 'billete';						
					}
					$this->contabilidad->agregarDenominacionRetiroParcial($colon->denominacion, $colon->cantidad, $tipo, 'colones', $retiro);					
				}
				
				foreach($dolares as $dolar){
					$this->contabilidad->agregarDenominacionRetiroParcial($dolar->denominacion, $dolar->cantidad, 'billete', 'dolares', $retiro);					
				}
				
				
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario realizo un retiro parcial de: $cantidad",$data['Sucursal_Codigo'],'retiro_parcial');
				$retorno['status'] = 'success';
				unset($retorno['error']);
				$retorno['retiro'] = $retiro;
				$retorno['sucursal']= $data['Sucursal_Codigo'];
				$retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
				$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");
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