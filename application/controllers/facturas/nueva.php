<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class nueva extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('user','',TRUE);
		$this->load->model('factura','',TRUE);
		$this->load->model('cliente','',TRUE);
		$this->load->model('articulo','',TRUE);
		$this->load->model('configuracion','',TRUE);
		$this->load->model('empresa','',TRUE);
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['crear_factura'])
		{
		   redirect('accesoDenegado', 'location');
		}
	}

	function index()
	{
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		$this->load->helper(array('form'));
		//echo $this->factura->getConsecutivo($data['Sucursal_Codigo']);
		date_default_timezone_set("America/Costa_Rica");
		$fecha = date(DB_DATETIME_FORMAT, now());
		$conf_array = $this->configuracion->getConfiguracionArray();
		$data['c_array'] = $conf_array;
		$data['token_factura_temp'] = md5($fecha.$data['Usuario_Codigo'].$data['Sucursal_Codigo']);
		$data['javascript_cache_version'] = $this->javascriptCacheVersion;
		$this->load->view('facturas/view_nueva_factura', $data);
	}

	function getNombreCliente()
	{
		//En realidad no devuelve solo el nombre, sino devuelve mas atributos
		if(isset($_GET['cedula'])){ //Si existe el parametro
			$id_request=$_GET['cedula'];
			$arrayCliente = $this->cliente->getNombreCliente($id_request);
			if($arrayCliente){ //Si encontro al cliente
				include PATH_USER_DATA;
				$arrayCliente['tieneCreditosVencidos'] = $this->cliente->tieneCreditosVencidosSinPagar($id_request, $data['Sucursal_Codigo']);
				$arrayCliente['status'] = 'success';
			}else{
				$arrayCliente['status'] = 'error';
			}
			echo json_encode($arrayCliente);
			//echo $id_request;
		}
	}

	function getArticuloXML()
	{
		$id_request=$_GET['codigo'];
		$cedula=$_GET['cedula'];
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		//Eliminamos los caracteres no permitidos como: & ; /
		$id_request_clean = str_replace("&","",$id_request);
		$id_request_clean = str_replace(";","",$id_request_clean);
		//echo "cedula: ".$cedula."|";
		echo $this->articulo->getArticuloXML($id_request_clean, $cedula, $data['Sucursal_Codigo']);
	}

	function getArticuloJSON(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['codigo'])&&isset($_POST['cedula'])){
			$codigo_articulo = $_POST['codigo'];
			$cedula = $_POST['cedula'];
			if($this->cliente->existe_Cliente($cedula)){
				include PATH_USER_DATA;
				if($articulo = $this->articulo->getArticuloArray($codigo_articulo, $cedula, $data['Sucursal_Codigo'])){
					//if($articulo['inventario'] > 0){
						$requiereFE = $this->empresa->empresaUsaCabys($data['Sucursal_Codigo']);
						if((is_numeric($articulo['cabys']) && $requiereFE) || $requiereFE == false){
							$retorno['status'] = 'success';
							$retorno['articulo'] = $articulo;
							unset($retorno['error']);
							//print_r($this->userdata_wrap);
						}else{
							$retorno['error'] = '7'; //No tiene cabys asignado
						}
					//}else{
					//	$retorno['error'] = '6'; //No tiene inventario
					//}
				}else{
					$retorno['error'] = '5'; //No existe articulo
				}
			}else{
				$retorno['error'] = '4'; //Cedula no valida o no existe cliente
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}

	function crearFacturaTemporal()
	{
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		date_default_timezone_set("America/Costa_Rica");
	    $Current_datetime = date(DB_DATETIME_FORMAT, now());
		//Token es una combinacion del nombre del usuario codigo sucursal y hora
		$token = $data['Usuario_Nombre'].$data['Usuario_Codigo'].$data['Sucursal_Codigo'].$Current_datetime;
		//echo $token;
		$facturaTemporal = $this->factura->crearFacturaTemporal($token);
		if($facturaTemporal){echo $facturaTemporal;}
		else{ echo 'false';}
	}

	function agregarArticuloFactura()
	{
		//Solo vamos a guardar articulos que esten en el sistema
		//Los articulos genericos seran ingresados solo en la factura final
		$articulo = $_GET['datosArticulo'];
		$articuloARRAY=explode(',',$articulo);
		if($articuloARRAY[1]!='00') //Mientras no sea un producto generico
		{$flag = $this->factura->agregarArticuloFactura($articuloARRAY);}
		//$flag = $this->articulo->actualizarInventarioRESTA($articuloARRAY[1], 1);
		//	$this->articulo->actualizarCantidadProductoTemporal($codigo_articulo, $cantidad, $factura_temp_id);
		//if($flag){echo 'Se ingreso bien';}
		//else{echo 'No se ingreso';}
		//echo $articulo;
		//echo $flag;
	}

	function actualizarInventario(){
		$codigo_articulo=$_GET['codigo'];
		$operacion=$_GET['operacion'];
		$tokenFactura=$_GET['token'];
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		$operacionARRAY = explode(',',$operacion);

		//echo $operacionARRAY[0]."<br>";

		if($operacionARRAY[0]!='1'&&$operacionARRAY[0]!='2')//Si no es un codigo valido enviar error
		{echo '-1';} //Numero de error para 'No es una operacion admitida'
		else{ //Se continua la valoracion
			//echo 'Operacion: '.$operacionARRAY[0].' Cantidad: '.$operacionARRAY[1].' Producto: '.$codigo_articulo;
			if($operacionARRAY[0]=='1'){ //Se resta al inventario
				$cantidadActual = $this->articulo->inventarioActual($codigo_articulo, $data['Sucursal_Codigo']); //Obtenemos la cantidad actual del inventario
				if($cantidadActual<$operacionARRAY[1]) //Si la cantidad es mayor a la actual hay ERROR
				{echo '-2';} //Numero de error para 'Cantidad mayor a inventario actual'
				else{
					$this->articulo->actualizarInventarioFacturaTemporal($codigo_articulo, $data['Sucursal_Codigo'], $operacionARRAY[1], $tokenFactura, false);
					echo $this->articulo->actualizarInventarioRESTA($codigo_articulo, $operacionARRAY[1], $data['Sucursal_Codigo']);
				}
			}
			else if($operacionARRAY[0]=='2'){ //Se suma al inventario
				$this->articulo->actualizarInventarioFacturaTemporal($codigo_articulo, $data['Sucursal_Codigo'], $operacionARRAY[1], $tokenFactura, true);
				echo $this->articulo->actualizarInventarioSUMA($codigo_articulo, $operacionARRAY[1], $data['Sucursal_Codigo']);
			}
		}

		//$factura_temp_id=$_GET['facturatmp'];
		//echo $codigo_articulo;
		//echo $cantidad;
		//Primero hay que ver si el articulo esta ya en la factura temporal
		/*$articuloResult = $this->getArticuloFromFacturaTemporal($factura_temp_id, $codigo_articulo);
		if($articuloResult){ //Si el articulo ya esta en la factura
			//echo 'Si esta';
			$cantidad_inicial_inventario;	//Obtenemos la cantidad inicial del inventario
			foreach($articuloResult as $row)
			{
				$cantidad_inicial_inventario = $row->Factura_Temporal_Cantidad_Bodega;
			}
			$cantidad_final = $cantidad_inicial_inventario - $cantidad;
			if($cantidad_final<0){echo '-2';} //Numero de error para 'Cantidad entrante mayor al inventario'
			else{
				$flag = $this->articulo->actualizarInventario($codigo_articulo, $cantidad_final);
				echo $flag;
			}
		}
		else{ //No esta en la factura
			echo '-1'; //Numero de error para 'No esta dentro de la factura el producto'
		}*/

		/*$cantidadAnterior = $this->articulo->getCantidadArticuloFromFacturaTemporal($factura_temp_id, $codigo_articulo);
		//echo 'Cantidad anterior: '.$cantidadAnterior.' Cantidad: '.$cantidad.'<br>';
		$flag;
		if($cantidadAnterior==$cantidad){
				$flag = $this->articulo->actualizarInventarioSUMA($codigo_articulo, $cantidad);
				$this->articulo->actualizarCantidadProductoTemporal($codigo_articulo, 0, $factura_temp_id);
				echo $flag;
		}//No pasa nada
		else if($cantidadAnterior<$cantidad){
			//$flag ='11';
			$cantidadFinal = $cantidad - $cantidadAnterior;
			//echo 'cantidad final: '.$cantidadFinal.'<br>';
			$flag = $this->articulo->actualizarInventarioRESTA($codigo_articulo, $cantidadFinal);
			$this->articulo->actualizarCantidadProductoTemporal($codigo_articulo, $cantidad, $factura_temp_id);
			echo $flag;
		}
		else if($cantidadAnterior>$cantidad){
			//$flag ='22';
			$cantidadFinal = $cantidadAnterior - $cantidad;
			//echo 'cantidad final: '.$cantidadFinal.'<br>';
			$flag = $this->articulo->actualizarInventarioSUMA($codigo_articulo, $cantidadFinal);
			$this->articulo->actualizarCantidadProductoTemporal($codigo_articulo, $cantidad, $factura_temp_id);
			echo $flag;
		}
		//$flag = $this->articulo->actualizarInventario($codigo_articulo, $cantidad);
		//echo $flag;*/
	}

	function getArticuloFromFacturaTemporal($factura_codigo, $Codigo_articulo){
		return $this->articulo->getArticuloFromFacturaTemporal($factura_codigo, $Codigo_articulo);
	}

	function getNombresClientesBusqueda(){
		$nombre=$_GET['term'];
		//echo $nombre;
		$result = $this->cliente->getNombresClientesBusqueda($nombre);
		if($result){
			$response = '';
			foreach($result as $row)
			{
			    $results[] = array('value' => $row->Cliente_Nombre." ".$row->Cliente_Apellidos,
								   'id' => $row->Cliente_Cedula);
				//$response = $response."<a class='nombresBusqueda' href='javascript:;' onClick='setNombreClienteBusqueda(".$row->Cliente_Cedula.")'>".$row->Cliente_Nombre." ".$row->Cliente_Apellidos."</a><br>";
			}
			echo json_encode($results);
		}
		else{
			echo "No hay coincidencias. . .";
		}
	}

	function checkUSR(){
		$usuario=$_GET['user'];
		$contrasena=$_GET['pass'];
		$tipo=$_GET['tipo'];
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

		$repuesta = '-1';

		if($this->user->isAdministrador($usuario, $contrasena, $data['Sucursal_Codigo'])){
			$repuesta = '200'; //Si se encontro
			$codigo_usuario = $this->user->getIdFromUserID($usuario, $data['Sucursal_Codigo']);
			if($tipo=='1'){ //Autorizo un articulo generico
				$this->user->guardar_transaccion($codigo_usuario, "$usuario autorizo articulo generico, sesion de: ".$data['Usuario_Codigo'], $data['Sucursal_Codigo'],'autoriza');
			}elseif($tipo=='2'){  //Autorizo descuento
				$this->user->guardar_transaccion($codigo_usuario, "$usuario autorizo un descuento, sesion de: ".$data['Usuario_Codigo'], $data['Sucursal_Codigo'],'autoriza');
			}elseif($tipo=='3'){  //Autorizo anular factura
				$permisos = $this->user->get_permisos($codigo_usuario, $data['Sucursal_Codigo']);
				if(isset($permisos['anular_facturas'])&&$permisos['anular_facturas']){
					$this->user->guardar_transaccion($codigo_usuario, "$usuario autorizo anular factura, sesion de: ".$data['Usuario_Codigo'], $data['Sucursal_Codigo'],'autoriza');
				}else{
					$repuesta = '-1';
				}
			}
		}
                if($tipo == "11"){
                    if($this->user->login($usuario, $contrasena)){
                        $codigo_usuario = $this->user->getIdFromUserID($usuario, $data['Sucursal_Codigo']);
                        if($tipo=='11'){  //Autorizo anular factura
				$permisos = $this->user->get_permisos($codigo_usuario, $data['Sucursal_Codigo']);
				if(isset($permisos['procesar_proformas'])&&$permisos['procesar_proformas']){
					$this->user->guardar_transaccion($codigo_usuario, "$usuario autorizo editar proforma, sesion de: ".$data['Usuario_Codigo'], $data['Sucursal_Codigo'],'autoriza');
				}else{
					$repuesta = '-1';
				}
			}
                    }
                }

		echo $repuesta; //No se encontro
	}

	function crearPendiente(){
            if(isset($_POST['head'])&&isset($_POST['items'])&&isset($_POST['token'])){
                //Obtener las dos partes del post
                $info_factura = $_POST['head'];
                $items_factura = $_POST['items'];
                //Decodificar el JSON del post
                $info_factura = json_decode($info_factura, true);
                $items_factura = json_decode($items_factura, true);
                //Obtenemos la primera posicion del info_factura para obtener el array final
                $info_factura = $info_factura[0];

				include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
                if($arrayCliente = $this->cliente->getNombreCliente($info_factura['ce'])){
                    if(!$arrayCliente["actualizar"]){
						if($this->cliente->tieneCreditosVencidosSinPagar($info_factura['ce'], $data['Sucursal_Codigo']) === false){
							//Verificamos que vengan productos
							if(sizeOf($items_factura)>0){


								//Borramos la factura temporal
								$this->articulo->eliminarFacturaTemporal($_POST['token']);

								$resultadoExistencias = $this->checkExistenciaDeProductos($items_factura, $data['Sucursal_Codigo']);
								if($resultadoExistencias["status"]){
										if($consecutivo = $this->factura->crearfactura($info_factura['ce'], $info_factura['no'], $info_factura['cu'], $info_factura['ob'], $data['Sucursal_Codigo'], $data['Usuario_Codigo'], false)){
												$tieneArticulos = $this->agregarItemsFactura($items_factura, $consecutivo, $data['Sucursal_Codigo'], $data['Usuario_Codigo'], $info_factura['ce']); //Agregamos los items
												if($tieneArticulos === true){
													$this->actualizarCostosFactura($consecutivo, $data['Sucursal_Codigo']);
													$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ".$data['Usuario_Codigo']." envio a caja la factura consecutivo:$consecutivo", $data['Sucursal_Codigo'],'factura_envio');

													echo '7'; //El ingreso fue correcto
												}else{
													//Eliminamos cualquier articulos suelto que haya quedado
													$this->factura->eliminarArticulosFactura($consecutivo, $data['Sucursal_Codigo']);
													$this->factura->eliminarFacturaPorFallo($consecutivo, $data['Sucursal_Codigo']);
													echo 'No se pudo agregar los productos a la factura';
												}
										}else{
												echo 'Hubo un error al crear encabezado de la factura'; //Error al crear la factura
										}
								}else{
										$articulos = "";
										foreach($resultadoExistencias["articulos"] as $arti){
												$articulos .= "-> Código: {$arti["codigo"]} Cantidad Disponible:  {$arti["inventario"]}<br>";
										}
										echo 'Los siguientes artículos no tienen suficiente inventario: <br>'.$articulos; // No hay suficiente existencia
								}
							}else{
								echo 'Problema cargando información de los artículos'; //No vienen productos
							}
						}else{
							echo '¡Este cliente tiene créditos vencidos que debe pagar! <BR>Por favor informarle al cliente ponerse al día para poder facturarle de nuevo.';
						}
                    }else{
                        echo 'El cliente ingresado debe actualizar sus datos para poder facturar.<br> Favor actualizar datos del cliente.';
                    }
                }else{
                    echo 'Cliente ingresado no existe';
                }
            }else{
                echo 'URL mal formado, por favor reportar al administrador';
            } //Numero de error mal post
        }

	function checkExistenciaDeProductos($items_factura, $sucursal){
		$r["status"] = true;
		$r["articulos"] = array();
		foreach($items_factura as $item){
		//{co:codigo, de:descripcion, ca:cantidad, ds:descuento, pu:precio_unitario, ex:exento}
			if($item['co']=='00'){ //Si es generico
					continue;
			}else{ //Si es normal
				if($articulo = $this->articulo->existe_Articulo($item['co'], $sucursal)){ //Verificamos que el codigo exista
					$articulo = $articulo[0];
					if($articulo->Articulo_Cantidad_Inventario < $item['ca'] || $articulo->Articulo_Cantidad_Inventario == 0){
						$r["status"] = false;
						array_push($r["articulos"], array("codigo"=>$item['co'],"inventario"=>$articulo->Articulo_Cantidad_Inventario));
					}
				}
			}

		}
		return $r;
	}

	function agregarItemsFactura($items_factura, $consecutivo, $sucursal, $vendedor, $cliente){

		foreach($items_factura as $item){
		//{co:codigo, de:descripcion, ca:cantidad, ds:descuento, pu:precio_unitario, ex:exento}
			if($item['co']==='00'){ //Si es generico
					$this->factura->addItemtoInvoice($item['co'], $item['de'], $item['ca'], $item['ds'], $item['ex'], $item['re'], $item['pu'], $item['pu'], $consecutivo, $sucursal, $vendedor, $cliente, ART_GEN_IMAGEN, ART_GEN_TIPO_CODIGO, ART_GEN_UNIDAD_MEDIDA, ART_GEN_CODIGO_CABYS, ART_GEN_IMPUESTO);
			}else{ //Si es normal
				if($articuloBD = $this->articulo->existe_Articulo($item['co'], $sucursal)){ //Verificamos que el codigo exista
					//Obtenemos los datos que no vienen en el JSON
					$articuloBD = $articuloBD[0];
					$descripcion = $articuloBD->Articulo_Descripcion;
					$imagen = $articuloBD->Articulo_Imagen_URL;
					$tipoCodigo = $articuloBD->TipoCodigo;
					$unidadMedida = $articuloBD->UnidadMedida;
					$codigoCabys = $articuloBD->CodigoCabys;
					$impuesto = $articuloBD->Impuesto;

					$precio = $this->articulo->getPrecioProducto($item['co'], $this->articulo->getNumeroPrecio($cliente), $sucursal);
					$precioFinal = $this->articulo->getPrecioProducto($item['co'], 1, $sucursal);

					$this->factura->addItemtoInvoice($item['co'], $descripcion, $item['ca'], $item['ds'], $item['ex'], $item['re'], $precio, $precioFinal, $consecutivo, $sucursal, $vendedor, $cliente, $imagen, $tipoCodigo, $unidadMedida, $codigoCabys, $impuesto);
					$this->articulo->actualizarInventarioRESTA($item['co'], $item['ca'], $sucursal);
				}
			}
		}

                return $this->factura->getArticulosFactura($consecutivo, $sucursal) !== false;
        }

	function actualizarCostosFactura($consecutivo, $sucursal){
		$costosArray = $this->factura->getCostosTotalesFactura($consecutivo, $sucursal);
		$this->factura->updateCostosTotales($costosArray, $consecutivo, $sucursal);
	}

	function devolverProductos(){
		if(isset($_POST['token'])){
			if($articulos = $this->articulo->getProductosFacturaTemporal($_POST['token'])){
				include PATH_USER_DATA;
				//Devolvemos a inventario las cantidades de la factura temporal
				foreach($articulos as $articulo){
					$this->articulo->actualizarInventarioSUMA($articulo->Codigo_Articulo, $articulo->Cantidad, $data['Sucursal_Codigo']);
				}
				//Eliminamos la factura temporal
				$this->articulo->eliminarFacturaTemporal($_POST['token']);
			}
		}
	}

}// FIN DE LA CLASE


?>