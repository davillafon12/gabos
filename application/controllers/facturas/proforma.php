<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class proforma extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('factura','',TRUE);
		$this->load->model('cliente','',TRUE);
		$this->load->model('articulo','',TRUE);
		$this->load->model('configuracion','',TRUE);
		$this->load->model('proforma_m','',TRUE);
	}

	function index()
	{
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
                
                $permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['crear_proforma'])
		{	
		   redirect('accesoDenegado', 'location');
		}
                
                
		$this->load->helper(array('form'));
		//echo $this->factura->getConsecutivo($data['Sucursal_Codigo']);
		//date_default_timezone_set("America/Costa_Rica");
		//echo date("y/m/d : H:i:s", now());
		$conf_array = $this->configuracion->getConfiguracionArray();
		$data['c_array'] = $conf_array;
		$data['javascript_cache_version'] = $this->javascriptCacheVersion;
		$this->load->view('facturas/view_proforma', $data);	
	}
	
	function getNombreCliente()
	{
		$id_request=$_GET['cedula'];
		echo $this->cliente->getNombreCliente($id_request);
		//echo $id_request;
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
		$contraseña=$_GET['pass'];
		
		if($this->user->isAdministrador($usuario, $contraseña)){
			echo '200'; //Si se encontro
		}
		else{
			echo '-1'; //No se encontro
		}
	}
	
	function crear(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['head'])&&isset($_POST['items'])){
		    //Obtener las dos partes del post
			$info_factura = $_POST['head'];
			$items_factura = $_POST['items'];
			//Decodificar el JSON del post
			$info_factura = json_decode($info_factura, true);			
			$items_factura = json_decode($items_factura, true);
			//Obtenemos la primera posicion del info_factura para obtener el array final
			$info_factura = $info_factura[0];

			include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
			
			if($consecutivo = $this->proforma_m->crearProforma($info_factura['ce'], $info_factura['no'], $info_factura['cu'], $info_factura['ob'], $data['Sucursal_Codigo'], $data['Usuario_Codigo'])){
				$this->agregarItemsProforma($items_factura, $consecutivo, $data['Sucursal_Codigo'], $data['Usuario_Codigo'], $info_factura['ce']); //Agregamos los items				
				$this->actualizarCostosProforma($consecutivo, $data['Sucursal_Codigo']);
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ".$data['Usuario_Codigo']." creo la proforma consecutivo:$consecutivo", $data['Sucursal_Codigo'],'factura_envio');
				
				//Para efecto de impresion
				$retorno['consecutivo']= $consecutivo;
				$retorno['sucursal']= $data['Sucursal_Codigo'];
				//Para efecto de impresion
				$retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
				$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");
	
				$retorno['status'] = 'success';
				unset($retorno['error']);
				
				//echo '7'; //El ingreso fue correcto											
			}else{
				$retorno['error'] = '11'; //Error al crear la factura
			}			
		}
		else{
			$retorno['error'] = '10';
		} //Numero de error mal post
		echo json_encode($retorno);
	}
	
	function agregarItemsProforma($items_factura, $consecutivo, $sucursal, $vendedor, $cliente){
		
		foreach($items_factura as $item){
			
		//{co:codigo, de:descripcion, ca:cantidad, ds:descuento, pu:precio_unitario, ex:exento}
			if($item['co']=='00'){ //Si es generico					
					$this->proforma_m->addItemtoInvoice($item['co'], $item['de'], $item['ca'], $item['ds'], $item['ex'], $item['re'], $item['pu'], $item['pu'], $consecutivo, $sucursal, $vendedor, $cliente, '00.png', '01','Unid');
			}else{ //Si es normal					
				if($this->articulo->existe_Articulo($item['co'], $sucursal)){ //Verificamos que el codigo exista
					//Obtenemos los datos que no vienen en el JSON
					$descripcion = $this->articulo->getArticuloDescripcion($item['co'], $sucursal);
					$imagen = $this->articulo->getArticuloImagen($item['co'], $sucursal);
					$precio = $this->articulo->getPrecioProducto($item['co'], $this->articulo->getNumeroPrecio($cliente), $sucursal);
					$precioFinal = $this->articulo->getPrecioProducto($item['co'], 1, $sucursal);
					$tipoCodigo = $this->articulo->getArticuloTipoCodigo($item['co'], $sucursal);
					$unidadMedida = $this->articulo->getArticuloUnidadMedida($item['co'], $sucursal);
					$this->proforma_m->addItemtoInvoice($item['co'], $descripcion, $item['ca'], $item['ds'], $item['ex'], $item['re'], $precio, $precioFinal, $consecutivo, $sucursal, $vendedor, $cliente, $imagen, $tipoCodigo, $unidadMedida);
				}
			}
			
		}
		//$this->factura->addItemtoInvoice($codigo, $descripcion, $cantidad, $descuento, $exento, $precio, $consecutivo, $sucursal, $vendedor, $cliente);
	}
	
	function actualizarCostosProforma($consecutivo, $sucursal){
		$costosArray = $this->proforma_m->getCostosTotalesProforma($consecutivo, $sucursal);
		$this->proforma_m->updateCostosTotales($costosArray, $consecutivo, $sucursal);
	}
	
	function descontarArticulosProforma(){
			$retorno['status'] = 'error';
			$retorno['error'] = 'No se pudo realizar el descuento de artículos.';
			if(isset($_POST['consecutivo']) && trim($_POST['consecutivo']) != ""){
					include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
					$consecutivo = trim($_POST['consecutivo']);
					$sucursal = $data['Sucursal_Codigo'];
					if($this->proforma_m->getProformasHeaders($consecutivo, $sucursal)){
							if(!$this->proforma_m->getProformaConArticulosDescontados($consecutivo, $sucursal)){
									if($articulos = $this->proforma_m->getArticulosProforma($consecutivo, $sucursal)){
											if($this->valorarSiHayExistenciaDeProductos($articulos, $sucursal)){
													foreach($articulos as $art){
															$this->articulo->actualizarInventarioRESTA($art->Articulo_Proforma_Codigo, $art->Articulo_Proforma_Cantidad, $sucursal);
													}
													$this->proforma_m->marcarProformaConDescuentoProductos($consecutivo, $sucursal);
													unset($retorno['error']);
													$retorno['status'] = 'success'; 
											}else{
													$retorno['error'] = 'No hay suficiente existencia en inventario para realizar el descuento de artículos.';
											}
									}else{
											$retorno['error'] = 'La proforma no posee artículos.';
									}
							}else{
									$retorno['error'] = 'Los artículos de esta proforma ya fueron descontados.';
							}
					}else{
							$retorno['error'] = 'La proforma no existe.';
					}
			}else{
					$retorno['error'] = 'URL con formato indebido.';
			}	
			echo json_encode($retorno);
	}
	
	function valorarSiHayExistenciaDeProductos($articulos, $sucursal){
			foreach($articulos as $art){
					if($art->Articulo_Proforma_Codigo != '00'){
							if($articuloInventario = $this->articulo->get_Articulo($art->Articulo_Proforma_Codigo, $sucursal)){
									$cantidadProforma = $art->Articulo_Proforma_Cantidad;
									$cantidadInventario = $articuloInventario[0]->Articulo_Cantidad_Inventario;
									if($cantidadProforma > $cantidadInventario){
											return false;  //No hay suficiente en inventario
									}
							}else{
									return false; //No existe codigo
							}
					}
			}
			return true;
	}
	
	function convertirEnFactura(){
		$retorno['status'] = 'error';
		$retorno['error'] = 'No se pudo realizar la conversión.';
		if(isset($_POST['consecutivo']) && trim($_POST['consecutivo']) != ""){
				include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
				$consecutivo = trim($_POST['consecutivo']);
				$sucursal = $data['Sucursal_Codigo'];
				if($proformaHeaders = $this->proforma_m->getProformasHeaders($consecutivo, $sucursal)){
					$proformaHeaders = $proformaHeaders[0];
					if($proformaHeaders->Proforma_Estado == "pendiente" || $proformaHeaders->Proforma_Estado == "pagada" || $proformaHeaders->Proforma_Estado == "descontada" ){
						if($articulos = $this->proforma_m->getArticulosProforma($consecutivo, $sucursal)){
								if($this->valorarSiHayExistenciaDeProductos($articulos, $sucursal)){
									if($consecutivoFactura = $this->factura->crearfactura($proformaHeaders->TB_03_Cliente_Cliente_Cedula, $proformaHeaders->Proforma_Nombre_Cliente, $proformaHeaders->Proforma_Moneda, "", $data['Sucursal_Codigo'], $proformaHeaders->Proforma_Vendedor_Codigo, false)){
										$this->agregarItemsFactura($articulos, $consecutivoFactura, $data['Sucursal_Codigo'], $proformaHeaders->Proforma_Vendedor_Codigo, $proformaHeaders->TB_03_Cliente_Cliente_Cedula);
										$this->actualizarCostosFactura($consecutivoFactura, $sucursal);
										$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ".$data['Usuario_Codigo']." convirtió la proforma $consecutivo en la factura pendiente $consecutivoFactura", $data['Sucursal_Codigo'],'factura_envio');
										foreach($articulos as $art){
												$this->articulo->actualizarInventarioRESTA($art->Articulo_Proforma_Codigo, $art->Articulo_Proforma_Cantidad, $sucursal);
										}
										$this->proforma_m->marcarComoProformaFacturada($consecutivo, $sucursal);
										unset($retorno['error']);
										$retorno['status'] = 'success';
										$retorno['consecutivo'] = $consecutivoFactura;
									}
								}else{
										$retorno['error'] = 'No hay suficiente existencia en inventario para realizar la factura.';
								}
						}else{
								$retorno['error'] = 'La proforma no posee artículos.';
						}
					}else{
						$retorno['error'] = 'La proforma ya fue facturada o anulada.';
					}
				}else{
						$retorno['error'] = 'La proforma no existe.';
				}
		}else{
				$retorno['error'] = 'URL con formato indebido.';
		}	
		echo json_encode($retorno);
	}
	
	function agregarItemsFactura($items_factura, $consecutivo, $sucursal, $vendedor, $cliente){
		foreach($items_factura as $item){
			if($item->Articulo_Proforma_Codigo == '00'){ //Si es generico					
					$this->factura->addItemtoInvoice($item->Articulo_Proforma_Codigo, $item->Articulo_Proforma_Descripcion, $item->Articulo_Proforma_Cantidad, $item->Articulo_Proforma_Descuento, $item->Articulo_Proforma_Exento, $item->Articulo_Proforma_No_Retencion, $item->Articulo_Proforma_Precio_Unitario, $item->Articulo_Proforma_Precio_Unitario, $consecutivo, $sucursal, $vendedor, $cliente,'','01','Unid');
			}else{ //Si es normal					
				if($this->articulo->existe_Articulo($item->Articulo_Proforma_Codigo, $sucursal)){ //Verificamos que el codigo exista
					
					$this->factura->addItemtoInvoice($item->Articulo_Proforma_Codigo, $item->Articulo_Proforma_Descripcion, $item->Articulo_Proforma_Cantidad, $item->Articulo_Proforma_Descuento, $item->Articulo_Proforma_Exento, $item->Articulo_Proforma_No_Retencion, $item->Articulo_Proforma_Precio_Unitario, $item->Articulo_Proforma_Precio_Final, $consecutivo, $sucursal, $vendedor, $cliente, $item->Articulo_Proforma_Imagen, $item->TipoCodigo, $item->UnidadMedida);
				}
			}
			
		}
	}

	function actualizarCostosFactura($consecutivo, $sucursal){
		$costosArray = $this->factura->getCostosTotalesFactura($consecutivo, $sucursal);
		$this->factura->updateCostosTotales($costosArray, $consecutivo, $sucursal);
	}
	
	function anularProforma(){
		$retorno['status'] = 'error';
		$retorno['error'] = 'No se pudo realizar la conversión.';
		if(isset($_POST['consecutivo']) && trim($_POST['consecutivo']) != ""){
				include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
				$consecutivo = trim($_POST['consecutivo']);
				$sucursal = $data['Sucursal_Codigo'];
				if($proformaHeaders = $this->proforma_m->getProformasHeaders($consecutivo, $sucursal)){
					$proformaHeaders = $proformaHeaders[0];
					if($proformaHeaders->Proforma_Estado == "pendiente"){
						$this->proforma_m->marcarComoProformaAnulada($consecutivo, $sucursal);
						unset($retorno['error']);
						$retorno['status'] = 'success';
					}else{
						$retorno['error'] = 'La proforma ya fue facturada, descontada, pagada o anulada.';
					}
				}else{
						$retorno['error'] = 'La proforma no existe.';
				}
		}else{
				$retorno['error'] = 'URL con formato indebido.';
		}	
		echo json_encode($retorno);
	}
	
	function marcarComoPagada(){
		$retorno['status'] = 'error';
		$retorno['error'] = 'No se pudo realizar la conversión.';
		if(isset($_POST['consecutivo']) && trim($_POST['consecutivo']) != ""){
				include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
				$consecutivo = trim($_POST['consecutivo']);
				$sucursal = $data['Sucursal_Codigo'];
				if($proformaHeaders = $this->proforma_m->getProformasHeaders($consecutivo, $sucursal)){
					$proformaHeaders = $proformaHeaders[0];
					if($proformaHeaders->Proforma_Estado != "anulada"){
						$this->proforma_m->marcarComoProformaPagada($consecutivo, $sucursal);
						unset($retorno['error']);
						$retorno['status'] = 'success';
					}else{
						$retorno['error'] = 'La proforma está anulada.';
					}
				}else{
						$retorno['error'] = 'La proforma no existe.';
				}
		}else{
				$retorno['error'] = 'URL con formato indebido.';
		}	
		echo json_encode($retorno);
	}
	
	function fijarProforma(){
                include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
                
                $permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['procesar_proformas'])
		{	
		   redirect('accesoDenegado', 'location');
		}
                
		$this->load->helper(array('form'));
		$conf_array = $this->configuracion->getConfiguracionArray();
		$data['c_array'] = $conf_array;
                $data['javascript_cache_version'] = $this->javascriptCacheVersion;
		$this->load->view('facturas/fijar_proforma', $data);	
	}
	
	function getFacturasSinProcesar(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['cliente'])){
			$cliente = $_POST['cliente'];
			include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
			if($facturas = $this->proforma_m->getProformasSinProcesar($cliente, $data['Sucursal_Codigo'])){
				unset($retorno['error']);
				$retorno['status'] = 'success';
				$retorno['facturas'] = $facturas;
			}else{
				//No hay facturas
				$retorno['error'] = '3';
			}	
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);
	}
	
	function cambiarProforma(){
			$retorno['status'] = 'error';
			$retorno['error'] = 'cf_1';
			if(isset($_POST['items'])&&isset($_POST['consecutivo'])){
		  //Obtener las dos partes del post
			$items_factura = $_POST['items'];
			$consecutivo = $_POST['consecutivo'];
			//Decodificar el JSON del post		
			$items_factura = json_decode($items_factura, true);
			//Obtenemos la primera posicion del info_factura para obtener el array final
			$observaciones = $_POST["observaciones"];			
			include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
			
			if(sizeOf($items_factura)>0){
					//ce:cedula_field, no:nombre_field, cu:tipo_moneda, ob:observaciones
					//CAMBIAMOS EL ENCABEZADO
					$datosHead = array(
										'Proforma_Observaciones'=>$observaciones
									);
														
					//ELIMINAMOS LOS PRODUCTOS
					$this->proforma_m->eliminarProductosDeProforma($consecutivo, $data['Sucursal_Codigo']);
					
					$this->proforma_m->actualizarHeadProforma($datosHead, $consecutivo, $data['Sucursal_Codigo']);
								
					//LOS VOLVEMOS A AGREGAR LOS NUEVOS
					$cliente = $this->proforma_m->getCliente($consecutivo, $data['Sucursal_Codigo']);
					
					$vendedor = $this->proforma_m->getVendedor($consecutivo, $data['Sucursal_Codigo']);
					
					
					$this->agregarItemsProforma($items_factura, $consecutivo, $data['Sucursal_Codigo'], $vendedor, $cliente);
					
					$this->actualizarCostosProforma($consecutivo, $data['Sucursal_Codigo']);				
					$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ".$data['Usuario_Codigo']." edito la proforma consecutivo:$consecutivo", $data['Sucursal_Codigo'],'proforma_edicion');
					
					unset($retorno['error']);
					$retorno['status'] = 'success';
			}else{
					$retorno['error'] = 'cf_3'; //No hay productos
			}
		}else{
			$retorno['error'] = 'cf_2'; //URL con mal formato
		} 
		echo json_encode($retorno);
	}
	
	function procesarProforma(){
		$retorno['status'] = 'error';
		$retorno['error'] = 'No se pudo procesar la proforma';
		if(isset($_POST['consecutivo'])){
			$consecutivo = $_POST['consecutivo'];
			include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
			if($this->proforma_m->existe_Proforma($consecutivo, $data['Sucursal_Codigo'])){
				$this->proforma_m->marcarComoProformaPendiente($consecutivo, $data['Sucursal_Codigo']);
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ".$data['Usuario_Codigo']." procesó la proforma consecutivo:$consecutivo", $data['Sucursal_Codigo'],'proforma_procesar');
				
				unset($retorno['error']);
				$retorno['status'] = 'success';
			}else{
				//No hay facturas
				$retorno['error'] = 'No existe proforma';
			}	
		}else{
			//URL MALA
			$retorno['error'] = 'Mal formato de URL';
		}
		echo json_encode($retorno);
	}
 
}// FIN DE LA CLASE


?>