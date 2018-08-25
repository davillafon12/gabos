<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class traspaso extends CI_Controller {
 
	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);
		$this->load->model('configuracion','',TRUE);
		$this->load->model('articulo','',TRUE);
		$this->load->model('contabilidad','',TRUE);
		$this->load->model('familia','',TRUE);
		$this->load->model('factura','',TRUE);
	}

	function index()
	{
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
				
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['realizar_traspasos'])
		{
				redirect('accesoDenegado', 'location');						
		}
		$data['Familia_Empresas'] = $this->empresa->get_empresas_ids_array();
		$this->load->view("articulos/articulos_traspaso_tiendas", $data);		
	}
	
	
	function obtenerArticulo(){
			$retorno['status'] = 'error';
			$retorno['error'] = 'No se pudo procesar la solicitud.';
			if(isset($_POST['codigo']) && isset($_POST['sucursal']) && isset($_POST['sucursalRecibe'])){
				$codigo = trim($_POST['codigo']);
				$sucursal = trim($_POST['sucursal']);
				$sucursalRecibe = trim($_POST['sucursalRecibe']);
				if($codigo != '' && $sucursal != ''){
					if($this->empresa->getEmpresa($sucursal)){
						if($this->empresa->getEmpresa($sucursalRecibe)){
							if($clienteLiga = $this->empresa->getClienteLigaByEmpresa($sucursalRecibe)){
								if($articulo = $this->articulo->getArticuloArray($codigo, $clienteLiga->Cliente, $sucursal)){
										$retorno['status'] = 'success';
										$articulo['retencion'] = $this->getRetencionReal($clienteLiga->Cliente, $articulo['retencion']);
										$articulo['exento'] = $this->getExentoReal($clienteLiga->Cliente, $articulo['exento']);
										$retorno['articulo'] = $articulo;
										unset($retorno['error']);
								}else{
										$retorno['status'] = 'success';
										$retorno['articulo'] = 'no_existe';
										unset($retorno['error']);
								}
							}else{
									$retorno['error'] = 'La sucursal que recibe no posee un cliente ligado.';
							}
						}else{
								$retorno['error'] = 'La sucursal que recibe no existe.';
						}
					}else{
							$retorno['error'] = 'La sucursal de entrega no existe.';
					}
				}else{
						$retorno['error'] = 'Uno de los campos requeridos está vacío.';
				}
			}else{
					$retorno['error'] = 'URL con formato indebido.';
			}
			echo json_encode($retorno);
	}
	
	
	public function realizarTraspaso(){
		$retorno['status'] = 'error';
		$retorno['error'] = 'No se pudo procesar la solicitud.';
		if(isset($_POST['articulos']) && isset($_POST['sucursalEntrega']) && isset($_POST['sucursalRecibe'])){
			$articulos = trim($_POST['articulos']);
			$sucursal = trim($_POST['sucursalEntrega']);
			$sucursalRecibe = trim($_POST['sucursalRecibe']);
			if($articulos != '' && $sucursal != '' && $sucursalRecibe != ''){
				if($this->empresa->getEmpresa($sucursal)){
					if($this->empresa->getEmpresa($sucursalRecibe)){
						if($clienteLiga = $this->empresa->getClienteLigaByEmpresa($sucursalRecibe)){
							$articulos = json_decode($articulos);
							if(sizeof($articulos)>0){
								
								if($this->verificarExistenciaDeArticulos($articulos, $sucursal)){
									//Cargamos informacion adicional
									include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
									date_default_timezone_set("America/Costa_Rica");
									$fechaHoraActual = date("Y-m-d  H:i:s", now());
									$traspaso = $this->articulo->crearTraspasoInventario($sucursal, $sucursalRecibe, $fechaHoraActual, $data['Usuario_Codigo']);
									
									$this->traspasarProductosASucursal($articulos, $sucursalRecibe, $sucursal, $traspaso, $clienteLiga);
																
						 			//Guardamos la transaccion
						 			$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario creó el traspaso # $traspaso",$data['Sucursal_Codigo'],'crear_traspaso');
									
																												
									$retorno['status'] = 'success';
									unset($retorno['error']);
									$retorno['traspaso'] = $traspaso;
									//Para efecto de impresion
									$retorno['sucursal']= $data['Sucursal_Codigo'];
									$retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
									$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");
								}else{
									$retorno['error'] = 'Alguno de los artículos no existe o ya no tiene unidades suficientes en existencia.';
								}
							}else{
								$retorno['error'] = 'No hay artículos para realizar el traspaso.';
							}
						}else{
							$retorno['error'] = 'Sucursal que recibe no cuenta con una liga de cliente.';
						}
					}else{
							$retorno['error'] = 'La sucursal que recibe no existe.';
					}
				}else{
						$retorno['error'] = 'La sucursal de entrega no existe.';
				}
			}else{
					$retorno['error'] = 'Uno de los campos requeridos está vacío.';
			}
		}else{
			$retorno['error'] = 'URL con formato indebido.';
		}
		echo json_encode($retorno);
	}
	
	private function verificarExistenciaDeArticulos($articulos, $sucursal){
			foreach($articulos as $art){
					if($articuloBD = $this->articulo->getArticuloArray($art->codigo, 0, $sucursal)){
							if($articuloBD['inventario'] >= $art->cantidad){
									//No pasa nada
							}else{
									return false;
							}
					}else{
							return false;
					}
			}
			return true;
	}
	
	
	
	private function registrarArticulo($articulo, $sucursalRecibe, $sucursalEntrega, $datosSesion, $clienteLiga){
			$familiaCodigo = 0; //Usamos cero por ser familia base
			//Si articulo no existe en la sucursal que recibe debemos registrarlo
			//PERO hay que verificar la existencia de la familia (Para este caso usamos familia = 0 FAMILIA BASE)
			if(!$this->familia->existeFamilia($familiaCodigo, $sucursalRecibe)){
					//Si no existe la familia, tons la creamos
					$this->familia->registrar($familiaCodigo, "FAMILIA BASE", "Creada por sistema de consignación.", $sucursalRecibe, $datosSesion['Usuario_Nombre_Usuario']);
			}
			$conf_array = $this->configuracion->getConfiguracionArray();
			$porcentajeIVA = $conf_array['iva'];
			//Ya con la familia creada, proseguimos con el registro del articulo
			//Traemos los precios y demas informacion del articulo
			$articuloDeSucursalEntrega = $this->articulo->existe_Articulo($articulo->codigo,$sucursalEntrega);
			$codigoBarras = $articuloDeSucursalEntrega[0]->Articulo_Codigo_Barras;
			$cantidadDefectuosa = 0;
			$descuento = $articuloDeSucursalEntrega[0]->Articulo_Descuento;
			$retencion = $articuloDeSucursalEntrega[0]->Articulo_No_Retencion;
			$exento = $articuloDeSucursalEntrega[0]->Articulo_Exento;
			$imagen = $articuloDeSucursalEntrega[0]->Articulo_Imagen_URL;
			
			//Para el costo, tomamos el precio al que se le vendio a la sucursal y le quitamos el IVA
			$numeroPrecioCliente = $this->cliente->getNumeroPrecio($clienteLiga->Cliente);
			$precioUnidadReal = $this->articulo->getPrecioProducto($articulo->codigo, $numeroPrecioCliente, $sucursalEntrega);
			$descuentoProductoReal = $this->articulo->getDescuento($articulo->codigo, $sucursalEntrega, $clienteLiga->Cliente, $familiaCodigo, $descuento); 
			$costo = $precioUnidadReal - ($precioUnidadReal * ($descuentoProductoReal / 100));
			$costo -= $costo / (1 + $porcentajeIVA);
			
			$precio1 = $this->articulo->getPrecioProducto($articulo->codigo, 1, $sucursalEntrega);
			$precio2 = $this->articulo->getPrecioProducto($articulo->codigo, 2, $sucursalEntrega);
			$precio3 = $this->articulo->getPrecioProducto($articulo->codigo, 3, $sucursalEntrega);
			$precio4 = $this->articulo->getPrecioProducto($articulo->codigo, 4, $sucursalEntrega);
			$precio5 = $this->articulo->getPrecioProducto($articulo->codigo, 5, $sucursalEntrega);
			$this->articulo->registrar(	$articulo->codigo, 
																	$articulo->descripcion, 
																	$codigoBarras, 
																	$articulo->cantidad, 
																	$cantidadDefectuosa, 
																	$descuento, 
																	$imagen, 
																	$exento, 
																	$retencion, 
																	$familiaCodigo, 
																	$sucursalRecibe, 
																	$costo, 
																	$precio1, 
																	$precio2, 
																	$precio3, 
																	$precio4, 
																	$precio5);
	}
	
	private function traspasarProductosASucursal($articulos, $sucursalRecibe, $sucursalEntrega, $traspaso, $clienteLiga){
			include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
			foreach($articulos as $art){
					//Primero verificamos que exista en la sucursal que recibe, si no lo creamos
					if(!$this->articulo->existe_Articulo($art->codigo,$sucursalRecibe)){
							$this->registrarArticulo($art, $sucursalRecibe, $sucursalEntrega, $data, $clienteLiga);
					}else{
						//Actualizamos el inventario
						$this->articulo->actualizarInventarioSUMA($art->codigo, $art->cantidad, $sucursalRecibe);
						
					}
					
					
					//Indiferentemente de si registro o actualizo el articulo
					//debemos restar dicha cantidad del inventario de la sucursal que entrega
					$this->articulo->actualizarInventarioRESTA($art->codigo, $art->cantidad, $sucursalEntrega);
					
					$this->articulo->agregarArticuloTraspasoInventario($traspaso, $art->codigo, $art->cantidad, $art->descripcion);
			}
	}

}


?>