<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cambio extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('articulo','',TRUE);
		$this->load->model('empresa','',TRUE);
		$this->load->model('familia','',TRUE);
		$this->load->model('user','',TRUE);
		$this->load->model('bodega_m','',TRUE);
	}

	function index()
	{
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		
		if(!$permisos['cambio_codigo_articulo'])
		{	
		   redirect('accesoDenegado', 'location');
		}
		
		$empresas_actuales = $this->empresa->get_empresas_ids_array();
		$data['Familia_Empresas'] = $empresas_actuales;
		$this->load->view('articulos/cambio_codigo_view', $data);
	}
	
	function getArticulo(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['codigo']) && isset($_POST['sucursal'])){
			$codigo = $_POST['codigo'];
			$sucursal = $_POST['sucursal'];
			if(trim($codigo)!='' && trim($sucursal)!=''){
				if(is_numeric($codigo) && is_numeric($sucursal)){
					if($articulos = $this->articulo->existe_Articulo($codigo,$sucursal)){
						foreach($articulos as $articulo){
							$art['descripcion'] = $articulo->Articulo_Descripcion;
							$art['inventario'] = $articulo->Articulo_Cantidad_Inventario;
						}
						unset($retorno['error']);
						$retorno['status'] = 'success';
						$retorno['articulo'] = $art;
					}else{
						$retorno['error'] = '5'; //No existe articulo
					}
				}else{
					$retorno['error'] = '4'; //No son numericos
				}
			}else{
				$retorno['error'] = '3'; //Campos vacios 
			}
		}else{
			$retorno['error'] = '2'; //MALA URL
		}
		echo json_encode($retorno);
	}
}

?>