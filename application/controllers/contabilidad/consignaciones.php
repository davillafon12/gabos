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
		$this->load->model('factura','',TRUE);
	}

	function index()
	{
            redirect('home', 'location');
	}

	function crear(){
			include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
			$data['javascript_cache_version'] = $this->javascriptCacheVersion;
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
										$articulo['retencion_cliente'] = $clienteLiga->informacion["retencion"];
										$articulo['exento_cliente'] = $clienteLiga->informacion["exento"];
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
							 												include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
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
							 									 			$this->consignarProductosASucursal($articulos, $sucursalRecibe, $sucursalEntrega, $consignacionId, false);
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

	private function consignarProductosASucursal($articulos, $sucursalRecibe, $sucursalEntrega, $consignacion, $aplicarConsignacion){
            include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
            foreach($articulos as $art){
                //Primero verificamos que exista en la sucursal que recibe, si no lo creamos
                if(!$this->articulo->existe_Articulo($art->codigo,$sucursalRecibe)){
                    $this->registrarArticulo($art, $sucursalRecibe, $sucursalEntrega, $data);
                }


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

                if($aplicarConsignacion){
                    //Actualizamos el inventario
                    $this->articulo->actualizarInventarioSUMA($art->codigo, $art->cantidad, $sucursalRecibe);
                    //Sobreescribimos los precios
                    $this->articulo->actualizarPrecios($art->codigo, $sucursalRecibe, $precios);

                    //Indiferentemente de si registro o actualizo el articulo
                    //debemos restar dicha cantidad del inventario de la sucursal que entrega
                    $this->articulo->actualizarInventarioRESTA($art->codigo, $art->cantidad, $sucursalEntrega);
                }

                //Agregamos dicho articulo a la consignacion
                $articuloDeSucursalEntrega = $this->articulo->existe_Articulo($art->codigo,$sucursalEntrega);
				$imagen = $articuloDeSucursalEntrega[0]->Articulo_Imagen_URL;
                $this->contabilidad->registrarArticuloConsignacion($art->codigo, $art->descripcion, $art->cantidad, $art->descuento, $art->precio_unidad, $art->precio_total, $art->exento, $art->retencion, $imagen, $consignacion, $art->precio_final);

                if($aplicarConsignacion){
                    // Agregamos el articulo a la lista de consignaciones
                    if($larticulo = $this->contabilidad->getArticuloEnListaConsignacion($art->codigo, $sucursalEntrega, $sucursalRecibe, $art->precio_unidad, $art->descuento, $art->exento, $art->retencion, $art->precio_final)){
                        $nuevaCantidad = $larticulo->Cantidad + $art->cantidad;
                        $this->contabilidad->actualizarArticuloEnListaConsignacion($art->codigo, $nuevaCantidad, $art->precio_unidad, $sucursalEntrega, $sucursalRecibe);
                    }else{
						$tipoCodigo = $articuloDeSucursalEntrega[0]->TipoCodigo;
						$unidadMedida = $articuloDeSucursalEntrega[0]->UnidadMedida;
						$codigoCabys = $articuloDeSucursalEntrega[0]->CodigoCabys;
						$impuesto = $articuloDeSucursalEntrega[0]->Impuesto;
                        $this->contabilidad->registrarArticuloEnListaConsignacion($art->codigo, $art->descripcion, $art->cantidad, $art->descuento, $art->precio_unidad, $art->precio_total, $art->exento, $art->retencion, $imagen, $sucursalEntrega, $sucursalRecibe, $art->precio_final, $tipoCodigo, $unidadMedida, $codigoCabys, $impuesto);
                    }
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

			//Ya con la familia creada, proseguimos con el registro del articulo
			//Traemos los precios y demas informacion del articulo
			$articuloDeSucursalEntrega = $this->articulo->existe_Articulo($articulo->codigo,$sucursalEntrega);
			$codigoBarras = $articuloDeSucursalEntrega[0]->Articulo_Codigo_Barras;
			$cantidadBuena = 0; // AQUI SE PONE CERO; YA QUE SOLO SE REGISTRA, EL METODO QUE LLAMA A ESTA FUNCION SE ENCARGA DE AGREGAR LAS CANTIDADES
			$cantidadDefectuosa = 0;
			$descuento = $articuloDeSucursalEntrega[0]->Articulo_Descuento;
			$retencion = $articuloDeSucursalEntrega[0]->Articulo_No_Retencion;
			$exento = $articuloDeSucursalEntrega[0]->Articulo_Exento;
			$imagen = $articuloDeSucursalEntrega[0]->Articulo_Imagen_URL;
			$tipoCodigo = $articuloDeSucursalEntrega[0]->TipoCodigo;
			$unidadMedida = $articuloDeSucursalEntrega[0]->UnidadMedida;
			$codigoCabys = $articuloDeSucursalEntrega[0]->CodigoCabys;
			$impuesto = $articuloDeSucursalEntrega[0]->Impuesto;

			//El impuesto ahora se obtiene de la tabla de articulos, de los codigos CABYS, ya no se usa IVA general del sistema
			$porcentajeIVA = $impuesto;

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
										$cantidadBuena,
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
										$precio5,
										$tipoCodigo,
										$unidadMedida,
										$codigoCabys,
										$impuesto);
	}

	public function facturar(){
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['facturar_consignaciones'])
		{
				redirect('accesoDenegado', 'location');
		}
		$data['Familia_Empresas'] = $this->empresa->get_empresas_ids_array();
                $data['javascript_cache_version'] = $this->javascriptCacheVersion;
                $data['meta_config'] = $this->factura->getConfgArray();
		$this->load->view("contabilidad/facturar_consignaciones_view", $data);
	}


	public function getArticulosEnListaConsignados(){
		$retorno['status'] = 'error';
		$retorno['error'] = 'No se pudo procesar la solicitud';
		if(isset($_POST['SR']) && isset($_POST['SE'])){
			$sucursalRecibe = trim($_POST['SR']);
			$sucursalEntrega = trim($_POST['SE']);
			if($sucursalRecibe != '' && $sucursalEntrega != ''){
				if($this->empresa->getEmpresa($sucursalRecibe) && $this->empresa->getEmpresa($sucursalEntrega)){
					if($articulos = $this->contabilidad->getArticulosEnListaDeConsignacion($sucursalEntrega, $sucursalRecibe)){
						$retorno['status'] = 'success';
						unset($retorno['error']);

                                                foreach($articulos as $art){
                                                    $a = $this->articulo->existe_Articulo($art->Codigo,$sucursalRecibe);
                                                    if($a !== false){
                                                        $a = $a[0];
                                                        $art->Bodega = $a->Articulo_Cantidad_Inventario;
                                                    }
                                                }

						$retorno['articulos'] = $articulos;
                                                $clienteLiga = $this->empresa->getClienteLigaByEmpresa($sucursalRecibe);
                                                $isExento = false;
                                                if($clienteLiga){
                                                    $isExento = $this->cliente->clienteEsExentoDeIVA($clienteLiga->Cliente);
                                                }
                                                $retorno['isExento'] = $isExento;
					}else{
						$retorno['error'] = 'No existen artículos consignados entre las sucursales ingresadas';
					}
				}else{
					$retorno['error'] = 'Alguna de las sucursales ingresadas no existe';
				}
			}else{
				$retorno['error'] = 'Datos requeridos están vacíos';
			}
		}else{
			$retorno['error'] = 'URL con formato indebido';
		}
		echo json_encode($retorno);
	}


	function crearFactura(){
			$retorno['status'] = 'error';
			$retorno['error'] = 'No se pudo procesar la solicitud.';
			if(isset($_POST["sucursalRecibe"]) && isset($_POST["sucursalEntrega"]) &&
				 isset($_POST["articulos"]) && isset($_POST["devolver"]) && isset($_POST["soloDevolver"])){
				 	try{
						 	$sucursalEntrega = trim($_POST["sucursalEntrega"]);
						 	$sucursalRecibe = trim($_POST["sucursalRecibe"]);
						 	$articulos = json_decode($_POST["articulos"]);
							$debeDevolver = trim($_POST["devolver"]) == '1' ? true : false;
							$soloDevolver = trim($_POST["soloDevolver"]) == '1' ? true : false;

						 	if($this->empresa->getEmpresa($sucursalEntrega)){
							 		if($this->empresa->getEmpresa($sucursalRecibe)){
							 				if(sizeOf($articulos) > 0){
			 										if($clienteLiga = $this->empresa->getClienteLigaByEmpresa($sucursalRecibe)){
				 											if($this->hayProductosAProcesar($articulos, $debeDevolver)){
																	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
																	if($soloDevolver){
																		$this->devolverArticulosSeleccionados($articulos, $sucursalEntrega);
																		$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario realizo una devolucion de articulos sin facturar",$data['Sucursal_Codigo'],'facturar_consignacion');
																		$retorno['status'] = 'success';
																		unset($retorno['error']);
																	}else{
																		if($consecutivo = $this->factura->crearfactura($clienteLiga->Cliente, $clienteLiga->informacion['nombre'], 'colones', 'Factura Generada Por Consignación', $sucursalEntrega, $data['Usuario_Codigo'], false)){
																			$this->registrarArticuloEnFactura($articulos, $debeDevolver, $sucursalEntrega, $data['Usuario_Codigo'], $clienteLiga->Cliente, $consecutivo);

																			$this->actualizarCostosFactura($consecutivo, $sucursalEntrega);

																			$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario creo una factura a partir de una consignacion, se creo la factura: $consecutivo y decidio ".($debeDevolver ? "SI" : "NO")." devolver articulos",$data['Sucursal_Codigo'],'facturar_consignacion');
																			$retorno['status'] = 'success';
																			unset($retorno['error']);
																		}else{
																			$retorno['error'] = 'No se pudo crear la factura.';
																		}
																	}
				 											}else{
					 												$retorno['error'] = 'No hay artículos que facturar.';
				 											}
				 									}else{
				 											$retorno['error'] = 'La empresa que recibe consignación no tiene liga con algún cliente.';
							 						}
							 				}else{
							 						$retorno['error'] = 'No se ingresaron artículos para crear factura.';
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

	private function hayProductosAProcesar($articulos, $debeDevolver){
		foreach($articulos as $articulo){
			if($articulo->cantidad > 0){
				return true;
			}
		}
		return $debeDevolver ? true : false;
	}

	private function devolverArticulosSeleccionados($articulos, $sucursal){
		foreach($articulos as $articulo){
			if($articuloBD = $this->contabilidad->getArticuloEnListaConsignacionById($articulo->codigo)){
				$cantidadConsignadaAFacturar = $articulo->cantidad;
				$nuevaCantidad = $articuloBD->Cantidad - $cantidadConsignadaAFacturar;
				$this->contabilidad->eliminarArticuloDeListaConsignacionById($articulo->codigo);
				$this->articulo->actualizarInventarioSUMA($articuloBD->Codigo, $nuevaCantidad, $sucursal);
			}
		}
	}

	private function registrarArticuloEnFactura($articulos, $debeDevolver, $sucursal, $vendedor, $cliente, $factura){
		foreach($articulos as $articulo){
			if($articuloBD = $this->contabilidad->getArticuloEnListaConsignacionById($articulo->codigo)){

					$cantidadConsignadaAFacturar = $articulo->cantidad;


					$codigoCabys = $articuloBD->Codigo_Cabys;
					//Si el codigo cabys esta vacio, estamos cargando un articulo de una factura que no se guardo con CABYS
					//Entonces obtenemos el cabys de la tabla de articulos original
					if(trim($codigoCabys) == ""){
						$codigoCabys = $this->articulo->getCodigoCabysArticuloOriginal($articuloBD->Codigo, $articuloBD->Sucursal_Entrega);
						if($codigoCabys == false){
							//Si ya ni existe en articulos ponemos uno generico
							$codigoCabys = ART_GEN_CODIGO_CABYS;
						}
					}

					//Agregamos el articulo a la factura
					$this->factura->addItemtoInvoice(
						$articuloBD->Codigo,
						$articuloBD->Descripcion,
						$cantidadConsignadaAFacturar,
						$articuloBD->Descuento,
						$articuloBD->Exento,
						$articuloBD->Retencion,
						$articuloBD->Precio_Unidad,
						$articuloBD->Precio_Final,
						$factura,
						$sucursal,
						$vendedor,
						$cliente,
						$articuloBD->Imagen,
						$articuloBD->TipoCodigo,
						$articuloBD->UnidadMedida,
						$codigoCabys,
						$articuloBD->Impuesto
					);

					$nuevaCantidad = $articuloBD->Cantidad - $cantidadConsignadaAFacturar;

					if($nuevaCantidad == 0){
						//Eliminamos la fila ya que no hay mas articulos consignados
						$this->contabilidad->eliminarArticuloDeListaConsignacionById($articulo->codigo);
					}

					//Debemos devolver y la cantidad es mayor a cero
					if($debeDevolver && $nuevaCantidad > 0){
						//Si devuelve, eliminamos el articulo de la lista y luego lo agregamos al inventario de la sucursal de entrega
						$this->contabilidad->eliminarArticuloDeListaConsignacionById($articulo->codigo);
						$this->articulo-> actualizarInventarioSUMA($articuloBD->Codigo, $nuevaCantidad, $sucursal);
					}else{
						//Si no devuelve, tons solo actualizamos el valor de la cantidad de las seleccionadas
						$this->contabilidad->actualizarCantidadArticuloListaConsignacion($articulo->codigo, $nuevaCantidad);
					}
			}
		}
	}

	private function actualizarCostosFactura($consecutivo, $sucursal){
		$costosArray = $this->factura->getCostosTotalesFactura($consecutivo, $sucursal);
		$this->factura->updateCostosTotales($costosArray, $consecutivo, $sucursal);
	}

        public function reversarConsignacion(){
            include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
            $retorno['status'] = 'error';
            $retorno['error'] = 'No se pudo procesar la solicitud.';
            $permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

            if(isset($permisos['reversar_consignaciones']) && $permisos['reversar_consignaciones']){
                if(isset($_GET["sucursalRecibe"]) && isset($_GET["sucursalEntrega"]) && isset($_GET["consignacion"])){
                        try{
                            $sucursalEntrega = trim($_GET["sucursalEntrega"]);
                            $sucursalRecibe = trim($_GET["sucursalRecibe"]);
                            $consignacion = trim($_GET["consignacion"]);
                            if($this->empresa->getEmpresa($sucursalEntrega)){
                                if($this->empresa->getEmpresa($sucursalRecibe)){
                                    if($consignacion = $this->contabilidad->getConsignacion($consignacion)){
                                        if($articulos = $this->contabilidad->getArticulosDeConsignacion($consignacion->Id)){
                                            if($consignacion->Estado == "creada"){
                                                $this->contabilidad->anularConsignacion($consignacion->Id);
                                                $this->devolverProductosASucursal($articulos, $sucursalRecibe, $sucursalEntrega, $consignacion);
                                                unset($retorno['error']);
                                                $retorno['status'] = "success";
                                                $retorno['msg'] = "Se pudo reversar la consignacion con exito";
                                            }else{
                                                $retorno['error'] = "No se puede reversar una consignacion ya reversada";
                                            }
                                        }else{
                                            $retorno['error'] = "No se pudo cargar los articulos de dicha consignacion";
                                        }
                                    }else{
                                        $retorno['error'] = "Consignación no existe";
                                    }
                                }else{
                                    $retorno['error'] = "Compañia que recibe no existe";
                                }
                            }else{
                                $retorno['error'] = "Compañia que entrega no existe";
                            }
                        }catch(Exception $e){
                            $retorno['error'] = 'Error desconocido... Exception Thrown...';
                            var_dump($e);
                        }
                }else{
                    $retorno['error'] = "Por favor ingresar sucursalEntrega, sucursalRecibe, consignacion como parametros GET";
                }
            }else{
               $retorno['error'] = "Usted no tiene permisos para reversar una consignacion";
            }
            echo "<pre>";
            print_r($retorno);
        }

        /**
         * Esta funcion realiza el proceso contrario a la funcion consignarProductosASucursal()
         *
         * Se encarga de reversar la consignacion de los articulos
         *
         * @param type $articulos
         * @param type $sucursalRecibe
         * @param type $sucursalEntrega
         * @param type $consignacion
         */
        private function devolverProductosASucursal($articulos, $sucursalRecibe, $sucursalEntrega, $consignacion){
			include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
			foreach($articulos as $art){
                            $art->codigo = $art->Codigo;
                            $art->precio_total = $art->Precio_Unidad;
                            $art->cantidad = $art->Cantidad;
                            $art->precio_unidad = $art->Precio_Unidad;
                            $art->descuento = $art->Descuento;
                            $art->exento = $art->Exento;
                            $art->retencion = $art->Retencion;
                            $art->precio_final = $art->Precio_Final;

					//Primero verificamos que exista en la sucursal que recibe, si no lo creamos
					if(!$this->articulo->existe_Articulo($art->codigo,$sucursalEntrega)){
                        $this->registrarArticulo($art, $sucursalEntrega, $sucursalRecibe, $data);
					}


					//Si el articulo existe, solo debemos actualizar la info del mismo.
					$conf_array = $this->configuracion->getConfiguracionArray();
					$porcentajeIVA = $conf_array['iva'];
					//Para el costo, tomamos el precio al que se le vendio a la sucursal y le quitamos el IVA
					$precioUnidadReal = $art->precio_total / $art->cantidad; //El precio unidad que viene no cuenta con el descuento, tons el precio por unidad lo sacamos de esta manera
					$precios["p0"] = $precioUnidadReal - ($precioUnidadReal / (1 + $porcentajeIVA));
					$precios["p1"] = $this->articulo->getPrecioProducto($art->codigo, 1, $sucursalRecibe);
					$precios["p2"] = $this->articulo->getPrecioProducto($art->codigo, 2, $sucursalRecibe);
					$precios["p3"] = $this->articulo->getPrecioProducto($art->codigo, 3, $sucursalRecibe);
					$precios["p4"] = $this->articulo->getPrecioProducto($art->codigo, 4, $sucursalRecibe);
					$precios["p5"] = $this->articulo->getPrecioProducto($art->codigo, 5, $sucursalRecibe);
					//Actualizamos el inventario
					$this->articulo->actualizarInventarioSUMA($art->codigo, $art->cantidad, $sucursalEntrega);
					//Sobreescribimos los precios
					$this->articulo->actualizarPrecios($art->codigo, $sucursalEntrega, $precios);

					//Indiferentemente de si registro o actualizo el articulo
					//debemos restar dicha cantidad del inventario de la sucursal que entrega
					$this->articulo->actualizarInventarioRESTA($art->codigo, $art->cantidad, $sucursalRecibe);

					// Agregamos el articulo a la lista de consignaciones
					if($larticulo = $this->contabilidad->getArticuloEnListaConsignacion($art->codigo, $sucursalEntrega, $sucursalRecibe, $art->precio_unidad, $art->descuento, $art->exento, $art->retencion, $art->precio_final)){
                                            $nuevaCantidad = $larticulo->Cantidad - $art->cantidad;
                                            $this->contabilidad->actualizarArticuloEnListaConsignacion($art->codigo, $nuevaCantidad, $art->precio_unidad, $sucursalEntrega, $sucursalRecibe);
					}
			}
	}


        function editar(){
            include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
            $data['javascript_cache_version'] = $this->javascriptCacheVersion;
            $permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

            if(!$permisos['editar_consignaciones'])
            {
                redirect('accesoDenegado', 'location');
            }
            $conf_array = $this->configuracion->getConfiguracionArray();
            $data['Familia_Empresas'] = $this->empresa->get_empresas_ids_array();
            $data['porcentaje_iva'] = $conf_array['iva'];
            $data['cantidad_decimales'] = $conf_array['cantidad_decimales'];
            $data['aplicar_retencion'] = $conf_array['aplicar_retencion'];
            $this->load->view("contabilidad/consignaciones_editar_view", $data);
	}

        function getConsignacionesFiltrados(){
            $retorno["error"] = "No se pudo procesar su solicitud";
            $retorno["status"] = "error";

            $consigna = trim($_POST["consigna"]) == "-1" ? "" : trim($_POST["consigna"]);
            $recibe = trim($_POST["recibe"]) == "-1" ? "" : trim($_POST["recibe"]);
            $desde = trim($_POST["desde"]);
            $hasta = trim($_POST["hasta"]);

            if($consignaciones = $this->contabilidad->getConsignacionesFiltradas($consigna, $recibe, $desde, $hasta)){
                unset($retorno["error"]);
                $retorno["status"] = "success";
                $retorno["consignaciones"] = $consignaciones;
            }else{
                $retorno["error"] = "No hay consignaciones con los filtros seleccionados";
                $retorno["status"] = "error";
            }

            echo json_encode($retorno);
        }

        function getConsignacion(){
            include PATH_USER_DATA;
            $retorno["status"] = "error";
            $retorno["error"] = "No se pudo procesar su solicitud";

			$consignacion = trim($_POST["consignacion"]);
			$isConsulta = false;
			if(isset($_POST["consulta"])){
				$isConsulta = trim($_POST["consulta"]) == 1;
			}

            if($consignacion = $this->contabilidad->getConsignacionParaImpresion($consignacion)){
				if($consignacion->estado == "creada" || $isConsulta){
					if($articulos = $this->contabilidad->getArticulosDeConsignacionParaEditar($consignacion->consecutivo, $consignacion->sucursal_entrega)){
						unset($retorno["error"]);
						$retorno["status"] = "success";
						$consignacion->articulos = $articulos;
						$retorno["consignacion"] = $consignacion;
						$retorno['consecutivo'] = $consignacion->consecutivo;
						$retorno['sucursal']= $data['Sucursal_Codigo'];
						$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");
					}else{
					   $retorno["error"] = "No hay artículos para esta consignación";
					}
				}else{
					$retorno["error"] = "No se pueden editar consignaciones anuladas ni aplicadas";
				}
            }else{
                $retorno["error"] = "Número de consignación no existe";
            }
            echo json_encode($retorno);
        }

        function aplicarConsignacion(){
            $retorno['status'] = 'error';
            $retorno['error'] = 'No se pudo procesar la solicitud.';

            if(isset($_POST["consignacion"]) && isset($_POST["sucursalRecibe"]) && isset($_POST["sucursalEntrega"]) &&
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
                    $consignacionId = trim($_POST["consignacion"]);

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

                                if(is_numeric($costo) && is_numeric($iva) && is_numeric($retencion) && is_numeric($total) && is_numeric($porcentaje_iva) && is_numeric($consignacionId)){
                                    if($consignacionMetadata = $this->contabilidad->getConsignacion($consignacionId)){
                                        if($this->verificarExistenciaDeArticulos($articulos, $sucursalEntrega)){
                                            if($clienteLiga = $this->empresa->getClienteLigaByEmpresa($sucursalRecibe)){
                                                //Cargamos informacion adicional
                                                include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

                                                $this->contabilidad->eliminarArticulosDeConsignacion($consignacionId);

                                                $this->contabilidad->aplicarConsignacion(
                                                                            $costo,
                                                                            $total,
                                                                            $consignacionId
                                                                    );
                                                //Realizamos la consignacion de los articulos
                                                $this->consignarProductosASucursal($articulos, $sucursalRecibe, $sucursalEntrega, $consignacionId, true);
                                                //Guardamos la transaccion
                                                $this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario aplicó la consignación # $consignacionId",$data['Sucursal_Codigo'],'crear_consignacion');


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
                                        $retorno['error'] = 'Consignación a editar no existe.';
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

        function guardarConsignacion(){
             $retorno['status'] = 'error';
            $retorno['error'] = 'No se pudo procesar la solicitud.';

            if(isset($_POST["consignacion"]) && isset($_POST["sucursalRecibe"]) && isset($_POST["sucursalEntrega"]) &&
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
                    $consignacionId = trim($_POST["consignacion"]);

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

                                if(is_numeric($costo) && is_numeric($iva) && is_numeric($retencion) && is_numeric($total) && is_numeric($porcentaje_iva) && is_numeric($consignacionId)){
                                    if($consignacionMetadata = $this->contabilidad->getConsignacion($consignacionId)){
                                        if($this->verificarExistenciaDeArticulos($articulos, $sucursalEntrega)){
                                            if($clienteLiga = $this->empresa->getClienteLigaByEmpresa($sucursalRecibe)){
                                                //Cargamos informacion adicional
                                                include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

                                                $this->contabilidad->eliminarArticulosDeConsignacion($consignacionId);

                                                $this->contabilidad->guardarConsignacion(
                                                                            $costo,
                                                                            $total,
                                                                            $consignacionId
                                                                    );
                                                //Realizamos la consignacion de los articulos
                                                $this->consignarProductosASucursal($articulos, $sucursalRecibe, $sucursalEntrega, $consignacionId, false);
                                                //Guardamos la transaccion
                                                $this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario guardó la consignación # $consignacionId",$data['Sucursal_Codigo'],'crear_consignacion');


                                                $retorno['status'] = 'success';
                                                unset($retorno['error']);
                                            }else{
                                                            $retorno['error'] = 'La empresa que recibe consignación no tiene liga con algún cliente.';
                                            }
                                        }else{
                                                        $retorno['error'] = 'Alguno de los artículos no existe o ya no tiene unidades suficientes en existencia.';
                                        }
                                    }else{
                                        $retorno['error'] = 'Consignación a editar no existe.';
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
}


?>
