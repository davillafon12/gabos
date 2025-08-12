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
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		
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
				if(is_numeric($sucursal)){
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
	
	function realizarCambio(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['sucursal']) &&
			isset($_POST['articulos'])){
			$sucursal = $_POST['sucursal'];
			$articulos = json_decode($_POST['articulos']);
			
			if(is_numeric($sucursal) && $this->articulosSonNumericos($articulos)){
				if($this->empresa->getEmpresa($sucursal)){
					if($this->articulosACambiarExisten($articulos, $sucursal)){
						if($this->articulosAAbonarExisten($articulos, $sucursal)){
							if($this->articulosCantidadValida($articulos, $sucursal)){
							
								include PATH_USER_DATA;
								date_default_timezone_set("America/Costa_Rica");
								$fecha = date(DB_DATETIME_FORMAT, now());
								$numeroCambio = $this->articulo->crearCambioCodigo($sucursal, $fecha, $data['Usuario_Codigo']);
								$this->procesarCambios($articulos, $sucursal, $numeroCambio);
								$nombreSucursal = $this->empresa->getNombreEmpresa($sucursal);
								$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario realizo el cambio numero: $numeroCambio en sucursal $sucursal - $nombreSucursal",$data['Sucursal_Codigo'],'cambio');
								
								unset($retorno['error']);
								$retorno['status'] = 'success';
								$retorno['cambio'] = $numeroCambio;
							}else{
								$retorno['error'] = '7'; //la cantidad no es valida
							}
						}else{
							$retorno['error'] = '6'; //articulo a abonar no existe
						}
					}else{
						$retorno['error'] = '5'; //articulo a cambiar no existe
					}
				}else{
					$retorno['error'] = '4'; //Sucursal no existe
				}
			}else{
				$retorno['error'] = '3'; //Algun dato no es numerico, no es valido
			}
		}else{
			$retorno['error'] = '2'; //MALA URL
		}
		echo json_encode($retorno);
	}
	
	function articulosSonNumericos($articulos){
		foreach($articulos as $articulo){
			if(is_numeric($articulo->cantidad)){
				//No pasa nada
			}else{
				return false;
			}
		}		
		//Todos cumplen con ser numericos
		return true;
	}
	
	function articulosACambiarExisten($articulos, $sucursal){
		foreach($articulos as $articulo){
			if(!$this->articulo->existe_Articulo($articulo->cambiar,$sucursal)){
				return false;
			}
		}
		return true;
	}
	
	function articulosAAbonarExisten($articulos, $sucursal){
		foreach($articulos as $articulo){
			if(!$this->articulo->existe_Articulo($articulo->abonar,$sucursal)){
				return false;
			}
		}
		return true;
	}
	
	function articulosCantidadValida($articulos, $sucursal){
		foreach($articulos as $articulo){
			$cantidad = $articulo->cantidad;
			$cantidadBDCambiar = $this->articulo->inventarioActual($articulo->cambiar, $sucursal);
			if($cantidad>=1 && $cantidad<=$cantidadBDCambiar){
				//no pasa nada
			}else{
				return false;
			}
		}
		return true;
	}
	
	function procesarCambios($articulos, $sucursal, $cambio){
		foreach($articulos as $articulo){
			$this->articulo->actualizarInventarioRESTA($articulo->cambiar, $articulo->cantidad, $sucursal);
			$this->articulo->actualizarInventarioSUMA($articulo->abonar, $articulo->cantidad, $sucursal);
			
			$this->articulo->agregarArticuloCambioCodigo($cambio, $articulo->cambiar, $this->articulo->getArticuloDescripcion($articulo->cambiar, $sucursal), $articulo->abonar, $this->articulo->getArticuloDescripcion($articulo->abonar, $sucursal), $articulo->cantidad);
		}
	}
	
	/*                 CODIGO ANTERIOR DE CAMBIO DE ARTICULO
		if(isset($_POST['cod_cambiar']) &&
			isset($_POST['cod_abonar']) &&
			isset($_POST['sucursal']) &&
			isset($_POST['cantidad'])){
			
			$cambiar = $_POST['cod_cambiar'];
			$abonar = $_POST['cod_abonar'];
			$sucursal = $_POST['sucursal'];
			$cantidad = $_POST['cantidad'];
			
			if(is_numeric($cambiar) &&
				is_numeric($abonar) &&
				is_numeric($sucursal) &&
				is_numeric($cantidad)){
				
				if($this->empresa->getEmpresa($sucursal)){
					if($this->articulo->existe_Articulo($cambiar,$sucursal)){
						if($this->articulo->existe_Articulo($abonar,$sucursal)){
							$cantidadBDCambiar = $this->articulo->inventarioActual($cambiar, $sucursal);
							if($cantidad>=1 && $cantidad<=$cantidadBDCambiar){
								unset($retorno['error']);
								$retorno['status'] = 'success';
								
								//AGREGAR CAMBIO DE INVENTARIO
								
								$this->articulo->actualizarInventarioRESTA($cambiar, $cantidad, $sucursal);
								$this->articulo->actualizarInventarioSUMA($abonar, $cantidad, $sucursal);
								
								$nombreSucursal = $this->empresa->getNombreEmpresa($sucursal);
								include PATH_USER_DATA;
								$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario cambio $cantidad unidades de codigo $cambiar por $abonar en sucursal $sucursal - $nombreSucursal",$data['Sucursal_Codigo'],'cambio');
							}else{
								$retorno['error'] = '7'; //la cantidad no es valida
							}
						}else{
							$retorno['error'] = '6'; //articulo a abonar no existe
						}
					}else{
						$retorno['error'] = '5'; //articulo a cambiar no existe
					}				
				}else{	
					$retorno['error'] = '4'; //Sucursal no existe
				}				
			}else{
				$retorno['error'] = '3'; //Algun dato no es numerico, no es valido
			}
		}else{
			$retorno['error'] = '2'; //MALA URL
		}*/
}

?>