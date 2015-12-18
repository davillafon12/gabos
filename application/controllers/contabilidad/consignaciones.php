<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class consignaciones extends CI_Controller {
 
	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);
		$this->load->model('configuracion','',TRUE);
		$this->load->model('articulo','',TRUE);
		$this->load->model('contabilidad','',TRUE);
		$this->load->model('familia','',TRUE);
	}

	function index()
	{
			redirect('home', 'location');			
	}
	
	function crear(){
			include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
				
			$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
			if(!$permisos['crear_consignaciones'])
			{
					redirect('accesoDenegado', 'location');						
			}
			$conf_array = $this->configuracion->getConfiguracionArray();
			$data['Familia_Empresas'] = $this->empresa->get_empresas_ids_array();
			$data['porcentaje_iva'] = $conf_array['iva'];
			$data['cantidad_decimales'] = $conf_array['cantidad_decimales'];
			$data['aplicar_retencion'] = $conf_array['aplicar_retencion'];
			$this->load->view("contabilidad/consignaciones_view", $data);
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
	
	function getRetencionReal($cliente, $retencionDelArticulo){
			//Si cliente no aplica retencion mandar ese
			//Si cliente aplica retencion, enviar la retencion del articulo
			$clienteInfo = $this->cliente->getNombreCliente($cliente);
			return $clienteInfo['retencion'] == '1' ? '1' : $retencionDelArticulo;
	}
	
	function getExentoReal($cliente, $exentoDelArticulo){
			$clienteInfo = $this->cliente->getNombreCliente($cliente);
			if($clienteInfo['exento'] == '1'){ return '1';}
			else{ return $exentoDelArticulo;}
	}
	
	function consignarArticulos(){
			$retorno['status'] = 'error';
			$retorno['error'] = 'No se pudo procesar la solicitud.';
			if(isset($_POST["sucursalRecibe"]) && isset($_POST["sucursalEntrega"]) &&
				 isset($_POST["articulos"]) && isset($_POST["costo"]) &&
				 isset($_POST["iva"]) && isset($_POST["retencion"]) &&
				 isset($_POST["total"]) && isset($_POST["porcentaje_iva"])){
				 	try{
						 	$sucursalEntrega = trim($_POST["sucursalEntrega"]);
						 	$sucursalRecibe = trim($_POST["sucursalRecibe"]);
						 	$articulos = json_decode($_POST["articulos"]);
						 	$costo = trim($_POST["costo"]);
						 	$iva = trim($_POST["iva"]);
						 	$retencion = trim($_POST["retencion"]);
						 	$total = trim($_POST["total"]);
						 	$porcentaje_iva = trim($_POST["porcentaje_iva"]);
						 	
						 	if($this->empresa->getEmpresa($sucursalEntrega)){
							 		if($this->empresa->getEmpresa($sucursalRecibe)){
							 				if(sizeOf($articulos) > 0){
							 						//Quitamos la division de centenares
							 						$costo = str_replace(".","",$costo);
							 						$iva = str_replace(".","",$iva);
							 						$retencion = str_replace(".","",$retencion);
							 						$total = str_replace(".","",$total);
							 						
							 						//Cambiamos el divisor de decimales
							 						$costo = str_replace(",",".",$costo);
							 						$iva = str_replace(",",".",$iva);
							 						$retencion = str_replace(",",".",$retencion);
							 						$total = str_replace(",",".",$total);
							 					
							 						if(is_numeric($costo) && is_numeric($iva) && is_numeric($retencion) && is_numeric($total) && is_numeric($porcentaje_iva)){
							 								if($this->verificarExistenciaDeArticulos($articulos, $sucursalEntrega)){
							 										if($clienteLiga = $this->empresa->getClienteLigaByEmpresa($sucursalRecibe)){
							 												//Cargamos informacion adicional
							 												include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
							 												date_default_timezone_set("America/Costa_Rica");
							 												$fechaHoraActual = date("Y-m-d  H:i:s", now());
							 												$sucursal_recibe_exenta = $clienteLiga->informacion['exento'];
							 												$sucursal_recibe_no_retencion = $clienteLiga->informacion['retencion'];
							 												$sucursal_recibe_cliente = $clienteLiga->Cliente;
							 												
							 									 			$consignacionId = $this->contabilidad->crearConsignacion(
							 									 												$fechaHoraActual, 
							 									 												$porcentaje_iva, 
							 									 												$iva, 
							 									 												$retencion, 
							 									 												$costo, 
							 									 												$total, 
							 									 												$sucursal_recibe_exenta, 
							 									 												$sucursal_recibe_no_retencion, 
							 									 												$data['Usuario_Codigo'], 
							 									 												$sucursalEntrega, 
							 									 												$sucursalRecibe, 
							 									 												$sucursal_recibe_cliente
							 									 											);
							 									 			//Realizamos la consignacion de los articulos							
							 									 			$this->consignarProductosASucursal($articulos, $sucursalRecibe, $sucursalEntrega, $consignacionId);								
							 									 			//Guardamos la transaccion
							 									 			$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario creó la consignación # $consignacionId",$data['Sucursal_Codigo'],'crear_consignacion');
																			
																																						
																			$retorno['status'] = 'success';
																			unset($retorno['error']);
																			$retorno['consignacion'] = $consignacionId;
																			//Para efecto de impresion
																			$retorno['sucursal']= $data['Sucursal_Codigo'];
																			$retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
																			$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");
								 									}else{
								 											$retorno['error'] = 'La empresa que recibe consignación no tiene liga con algún cliente.';
								 									}
							 								}else{
							 										$retorno['error'] = 'Alguno de los artículos no existe o ya no tiene unidades suficientes en existencia.';
							 								}
							 						}else{
							 								$retorno['error'] = 'Alguno de los montos no tiene formato numerico.';
							 						}
							 				}else{
							 						$retorno['error'] = 'No se ingresaron artículos a consignar.';
							 				}
									}else{
											$retorno['error'] = 'Sucursal que recibe consignación no existe.';
									}
							}else{
									$retorno['error'] = 'Sucursal que consigna no existe.';
							}
					}catch(Exception $e){
							$retorno['error'] = 'Error desconocido... Exception Thrown...';
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
	
	private function consignarProductosASucursal($articulos, $sucursalRecibe, $sucursalEntrega, $consignacion){
			include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			foreach($articulos as $art){
					//Primero verificamos que exista en la sucursal que recibe, si no lo creamos
					if(!$this->articulo->existe_Articulo($art->codigo,$sucursalRecibe)){
							$this->registrarArticulo($art, $sucursalRecibe, $sucursalEntrega, $data);
					}else{
							//Si el articulo existe, solo debemos actualizar la info del mismo.
							$conf_array = $this->configuracion->getConfiguracionArray();
							$porcentajeIVA = $conf_array['iva'];
							//Para el costo, tomamos el precio al que se le vendio a la sucursal y le quitamos el IVA
							$precioUnidadReal = $art->precio_total / $art->cantidad; //El precio unidad que viene no cuenta con el descuento, tons el precio por unidad lo sacamos de esta manera
							$precios["p0"] = $precioUnidadReal - ($precioUnidadReal / (1 + $porcentajeIVA));
							$precios["p1"] = $this->articulo->getPrecioProducto($art->codigo, 1, $sucursalEntrega);
							$precios["p2"] = $this->articulo->getPrecioProducto($art->codigo, 2, $sucursalEntrega);
							$precios["p3"] = $this->articulo->getPrecioProducto($art->codigo, 3, $sucursalEntrega);
							$precios["p4"] = $this->articulo->getPrecioProducto($art->codigo, 4, $sucursalEntrega);
							$precios["p5"] = $this->articulo->getPrecioProducto($art->codigo, 5, $sucursalEntrega);
							//Actualizamos el inventario
							$this->articulo->actualizarInventarioSUMA($art->codigo, $art->cantidad, $sucursalRecibe);
							//Sobreescribimos los precios
							$this->articulo->actualizarPrecios($art->codigo, $sucursalRecibe, $precios);
					}
					//Indiferentemente de si registro o actualizo el articulo
					//debemos restar dicha cantidad del inventario de la sucursal que entrega
					$this->articulo->actualizarInventarioRESTA($art->codigo, $art->cantidad, $sucursalEntrega);
					//Agregamos dicho articulo a la consignacion
					$articuloDeSucursalEntrega = $this->articulo->existe_Articulo($art->codigo,$sucursalEntrega);
					$imagen = $articuloDeSucursalEntrega[0]->Articulo_Imagen_URL;
					$this->contabilidad->registrarArticuloConsignacion($art->codigo, $art->descripcion, $art->cantidad, $art->descuento, $art->precio_unidad, $art->precio_total, $art->exento, $art->retencion, $imagen, $consignacion);
			
					// Agregamos el articulo a la lista de conignaciones
					if($larticulo = $this->contabilidad->getArticuloEnListaConsignacion($art->codigo, $sucursalEntrega, $sucursalRecibe, $art->precio_unidad)){
							$nuevaCantidad = $larticulo->Cantidad + $art->cantidad;
							$this->contabilidad->actualizarArticuloEnListaConsignacion($art->codigo, $nuevaCantidad, $art->precio_unidad, $sucursalEntrega, $sucursalRecibe);
					}else{
							$this->contabilidad->registrarArticuloEnListaConsignacion($art->codigo, $art->descripcion, $art->cantidad, $art->descuento, $art->precio_unidad, $art->precio_total, $art->exento, $art->retencion, $imagen, $sucursalEntrega, $sucursalRecibe);
					}
			}
	}
	
	private function registrarArticulo($articulo, $sucursalRecibe, $sucursalEntrega, $datosSesion){
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
			$precioUnidadReal = $articulo->precio_total / $articulo->cantidad; //El precio unidad que viene no cuenta con el descuento, tons el precio por unidad lo sacamos de esta manera
			$costo = $precioUnidadReal - ($precioUnidadReal / (1 + $porcentajeIVA));
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
	
	
	/*
	* Esta funcion agrega el articulo a la lista que se cargara en el momento de facturar consignaciones
	*/
	private function ponerArticuloEnListaDeConsignacion(){
	
	}
}


?>