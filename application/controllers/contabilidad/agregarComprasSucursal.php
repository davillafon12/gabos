<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class agregarComprasSucursal extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);
		$this->load->model('factura','',TRUE);
		$this->load->model('bodega_m','',TRUE);
		$this->load->model('articulo','',TRUE);
		$this->load->model('familia','',TRUE);
		$this->load->model('contabilidad','',TRUE);
		$this->load->model('configuracion','',TRUE);
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
		if(isset($_POST['factura']) && isset($_POST['sucursal']) && isset($_POST['prefijo'])){
			$factura = $_POST['factura'];	
			$sucursal = $_POST['sucursal'];
			$prefijo = trim($_POST['prefijo']);
			if(is_numeric($factura)){
				if($productos = $this->factura->getItemsFactura($factura, $this->configuracion->getEmpresaDefectoTraspasoCompras())){ 
					if($this->empresa->getEmpresa($sucursal)){
						//Verifica que ambas sucursales sean diferentes
						if(trim($sucursal) != trim($this->configuracion->getEmpresaDefectoTraspasoCompras()))
						{
							//Verifica que la factura a traspasar no haya sido traspasado antes a esa sucursal
							if(!$this->contabilidad->facturaTraspasoHaSidoAplicada($factura, $this->configuracion->getEmpresaDefectoTraspasoCompras(), $sucursal))
							{
								include '/../get_session_data.php';
								date_default_timezone_set("America/Costa_Rica");
								$fecha = date("y/m/d : H:i:s", now());
								
								$traspaso = $this->contabilidad->crearTraspasoArticulos($this->configuracion->getEmpresaDefectoTraspasoCompras(), $sucursal, $data['Usuario_Codigo'], $fecha, $factura);
								
								//Traemos el array de configuracion para obtener el porcentaje
								$c_array = $this->getConfgArray();
								
								foreach($productos as $pro){
									$costo = $pro->Articulo_Factura_Precio_Unitario;
									$descuento = $pro->Articulo_Factura_Descuento;
									//Aplicamos descuento
									$costo -= $costo * ( $descuento / 100 );
									//Le quitamos el iva 
									$costo /= 1+(floatval($c_array['iva'])/100);
									$this->bodega_m->agregarCompra($pro->Articulo_Factura_Codigo, $pro->Articulo_Factura_Descripcion, $costo, $pro->Articulo_Factura_Cantidad, $fecha, $data['Usuario_Codigo'], $sucursal);
									$this->agregarAInventario($pro->Articulo_Factura_Codigo, $pro->Articulo_Factura_Cantidad, $pro->Articulo_Factura_Descripcion, $costo, $this->configuracion->getEmpresaDefectoTraspasoCompras(), $sucursal, $traspaso, $prefijo);
								}
								$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario agrego la factura #$factura como compras de la sucursal $sucursal",$data['Sucursal_Codigo'],'compras');
								
								$retorno['status'] = 'success';
								unset($retorno['error']);
								$retorno['traspaso'] = $traspaso;
								$retorno['sucursal']= $data['Sucursal_Codigo'];
								$retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
								$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");
							}
							else{
								$retorno['error'] = '7'; //La factura ya fue aplicada antes
							}
						}else{
							$retorno['error'] = '6'; //La sucursal a enviar no puede ser igual a la sucursal que envia
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
	
	private function agregarAInventario($codigo, $cantidad, $descripcion, $costo, $sucursalSalida, $sucursalEntrada, $traspaso, $prefijo){
		if($this->articulo->get_Articulo($prefijo."".$codigo, $sucursalEntrada)){
			//El articulo si esta creado en la sucursal de entrada
			$this->articulo->actualizarInventarioSUMA($prefijo."".$codigo, $cantidad, $sucursalEntrada);
		}else{
			//No existe debemos registrarlo
			$articulo = $this->articulo->get_Articulo($codigo, $sucursalSalida)[0];
			
			$familia = $articulo->TB_05_Familia_Familia_Codigo;
			//Revisamos que exista esa familia
			if(!$this->familia->es_codigo_usado($familia, $sucursalEntrada)){
				//Si no existe la creamos
				include '/../get_session_data.php';
				$fa = $this->familia->es_codigo_usado($familia, $sucursalSalida)[0];
				$this->familia->registrar($fa->Familia_Codigo, $fa->Familia_Nombre, $fa->Familia_Observaciones, $sucursalEntrada, $data['Usuario_Nombre']);
			}
			
			/*($articulo_Codigo, 
			   $articulo_Descripcion, 
			   $articulo_Codigo_Barras, 
			   $articulo_Cantidad_Inventario, 
			   $articulo_Cantidad_Defectuoso, 
			   $articulo_Descuento, 
			   $Articulo_Imagen_URL, 
			   $Articulo_Exento, 
			   $retencion, 
			   $TB_05_Familia_Familia_Codigo, 
			   $TB_02_Sucursal_Codigo, 
			   $costo, 
			   $precio1, 
			   $precio2, 
			   $precio3, 
			   $precio4, 
			   $precio5)*/
			$this->articulo->registrar(	$prefijo."".$codigo, 
										$descripcion, 
										$articulo->Articulo_Codigo_Barras, 
										$cantidad, 
										0, 
										0, 
										$articulo->Articulo_Imagen_URL, 
										$articulo->Articulo_Exento, 
										0,
										$familia, 
										$sucursalEntrada, 
										$costo, 
										$this->articulo->getPrecioProducto($codigo, 1, $sucursalSalida), 
										$this->articulo->getPrecioProducto($codigo, 2, $sucursalSalida), 
										$this->articulo->getPrecioProducto($codigo, 3, $sucursalSalida), 
										$this->articulo->getPrecioProducto($codigo, 4, $sucursalSalida), 
										$this->articulo->getPrecioProducto($codigo, 5, $sucursalSalida));
		}
		//Agregamos el producto al traspaso
		$this->contabilidad->agregarArticuloTraspaso($prefijo."".$codigo, $descripcion, $cantidad, $traspaso);
	}
	
	
	function getConfgArray()
	{
		return $this->configuracion->getConfiguracionArray();
	}
	
}

?>