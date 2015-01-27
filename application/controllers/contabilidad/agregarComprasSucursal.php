<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class agregarComprasSucursal extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);
		$this->load->model('configuracion','',TRUE);
		$this->load->model('factura','',TRUE);
		$this->load->model('bodega_m','',TRUE);
	}

	function index()
	{
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['compras_sucursales'])
		{
			redirect('accesoDenegado', 'location');			
		}
		
		$data['Familia_Empresas'] = $this->empresa->get_empresas_ids_array();
		$this->load->view('contabilidad/compras_sucursales_view', $data);
		
	}	
	
	function cargarFactura(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['factura'])){
			$factura = $_POST['factura'];			
			if(is_numeric($factura)){
				if($itemsFactura = $this->factura->getItemsFactura($factura, $this->configuracion->getEmpresaDefectoTraspasoCompras())){ 
					$retorno['status'] = 'success';
					unset($retorno['error']);	
					$retorno['productos'] = $itemsFactura;
				}else{
					$retorno['error'] = '4'; //Numero de factura no existe
				}
			}else{
				$retorno['error'] = '3'; //Numero de factura no valido
			}
		}else{
			$retorno['error'] = '2'; //URL MALA
		}
		echo json_encode($retorno);
	}
	
	function agregarCompras(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['factura']) && isset($_POST['sucursal'])){
			$factura = $_POST['factura'];	
			$sucursal = $_POST['sucursal'];
			if(is_numeric($factura)){
				if($productos = $this->factura->getItemsFactura($factura, $this->configuracion->getEmpresaDefectoTraspasoCompras())){ 
					if($this->empresa->getEmpresa($sucursal)){
						if(trim($sucursal) != trim($this->configuracion->getEmpresaDefectoTraspasoCompras()))
						{
							include '/../get_session_data.php';
							date_default_timezone_set("America/Costa_Rica");
							$fecha = date("y/m/d : H:i:s", now());
							foreach($productos as $pro){
								$costo = $pro->Articulo_Factura_Precio_Unitario;
								$descuento = $pro->Articulo_Factura_Descuento;
								$costo -= $costo * ( $descuento / 100 ); 
								$this->bodega_m->agregarCompra($pro->Articulo_Factura_Codigo, $pro->Articulo_Factura_Descripcion, $costo, $pro->Articulo_Factura_Cantidad, $fecha, $data['Usuario_Codigo'], $sucursal);
							}
							$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario agrego la factura #$factura como compras de la sucursal $sucursal",$data['Sucursal_Codigo'],'compras');
							$retorno['status'] = 'success';
							unset($retorno['error']);
						}else{
							$retorno['error'] = '6'; //La sucursal a enviar no puede ser igual ala sucursal que envia
						}
					}else{
						$retorno['error'] = '5'; //Sucursal no existe
					}
				}else{
					$retorno['error'] = '4'; //Numero de factura no existe
				}
			}else{
				$retorno['error'] = '3'; //Numero de factura no valido
			}
		}else{
			$retorno['error'] = '2'; //URL MALA
		}
		echo json_encode($retorno);
	}
	
	
}

?>