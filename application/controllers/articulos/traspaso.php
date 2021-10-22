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

			$tipo_codigo = $articuloDeSucursalEntrega[0]->TipoCodigo;
			$unidadmedida = $articuloDeSucursalEntrega[0]->UnidadMedida;
			$codigoCabys = $articuloDeSucursalEntrega[0]->CodigoCabys;
			$impuesto = $articuloDeSucursalEntrega[0]->Impuesto;

			//Para el costo, tomamos el precio al que se le vendio a la sucursal y le quitamos el IVA
			$numeroPrecioCliente = $this->cliente->getNumeroPrecio($clienteLiga->Cliente);
			$precioUnidadReal = $this->articulo->getPrecioProducto($articulo->codigo, $numeroPrecioCliente, $sucursalEntrega);
			$descuentoProductoReal = $this->articulo->getDescuento($articulo->codigo, $sucursalEntrega, $clienteLiga->Cliente, $familiaCodigo, $descuento);
			$costo = $precioUnidadReal - ($precioUnidadReal * ($descuentoProductoReal / 100));
			$costo -= $costo / (1 + $porcentajeIVA);

			$precio1O = $this->articulo->getPrecioProductoObject($articulo->codigo, 1, $sucursalEntrega);
			$precio2O = $this->articulo->getPrecioProductoObject($articulo->codigo, 2, $sucursalEntrega);
			$precio3O = $this->articulo->getPrecioProductoObject($articulo->codigo, 3, $sucursalEntrega);
			$precio4O = $this->articulo->getPrecioProductoObject($articulo->codigo, 4, $sucursalEntrega);
			$precio5O = $this->articulo->getPrecioProductoObject($articulo->codigo, 5, $sucursalEntrega);
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
										$precio1O->Precio_Monto,
										$precio2O->Precio_Monto,
										$precio3O->Precio_Monto,
										$precio4O->Precio_Monto,
										$precio5O->Precio_Monto,
										$tipo_codigo,
										$unidadmedida,
										$codigoCabys,
										$impuesto,
										$descuentoProductoReal,
										$precio1O->Precio_Descuento,
										$precio2O->Precio_Descuento,
										$precio3O->Precio_Descuento,
										$precio4O->Precio_Descuento,
										$precio5O->Precio_Descuento);
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

        public function traspasoEs(){
            $sucursalAPasar = trim(@$_GET["s"]) == "" ? 99999 : trim(@$_GET["s"]);
            $url = "http://192.168.10.27";
            if($this->empresa->getEmpresa($sucursalAPasar) && $sucursalAPasar != 4){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch,CURLOPT_URL,$url."/articulos/traspaso/getArticulosSucursal?s=".$sucursalAPasar);
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
                $datos = curl_exec($ch);
                curl_close($ch);
                //$datos = file_get_contents($url."/articulos/traspaso/getArticulosSucursal?s=".$sucursalAPasar);
                $datos = json_decode($datos, true);
                if(is_array($datos)){
                    echo "Borrando articulos actuales en BD<br>";
                    $this->articulo->borrarArticulosDeSucursalCompleto($sucursalAPasar);
                    echo "Articulos borrados<br>";
                    echo "Insertando ".sizeof($datos)." articulos<br>";
                    $articulosIngresados = 0;
                    $articulosConError = 0;
                    $articulosError = array();
                    foreach($datos as $art){
                        if($this->articulo->registrar($art["Articulo_Codigo"],
                                                    $art["Articulo_Descripcion"],
                                                    $art["Articulo_Codigo_Barras"],
                                                    $art["Articulo_Cantidad_Inventario"],
                                                    $art["Articulo_Cantidad_Defectuoso"],
                                                    $art["Articulo_Descuento"],
                                                    $art["Articulo_Imagen_URL"],
                                                    $art["Articulo_Exento"],
                                                    $art["Articulo_No_Retencion"],
                                                    $art["TB_05_Familia_Familia_Codigo"],
                                                    $art["TB_02_Sucursal_Codigo"],
                                                    $art["precios"]["p0"],
                                                    $art["precios"]["p1"],
                                                    $art["precios"]["p2"],
                                                    $art["precios"]["p3"],
                                                    $art["precios"]["p4"],
                                                    $art["precios"]["p5"])){
                            $articulosIngresados++;
                        }else{
                            $articulosConError++;
                            array_push($articulosError, $art["Articulo_Codigo"]);
                        }
                    }
                    echo "Insercion finalizada<br>";
                    echo "$articulosIngresados articulos insertados<br>";
                    echo "$articulosConError articulos no insertados (Error):<br>";
                    foreach($articulosError as $ae){
                        echo "-$ae <br>";
                    }
                }else{
                    die("Error al cargar los articulos desde remoto");
                }
            }else{
                die("Sucursal ingresada no existe");
            }
        }

}


?>