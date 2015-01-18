<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class nueva extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('factura','',TRUE);
		$this->load->model('cliente','',TRUE);
		$this->load->model('articulo','',TRUE);
		$this->load->model('XMLParser','',TRUE);
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['crear_factura'])
		{	
		   redirect('accesoDenegado', 'location');
		}
	}

	function index()
	{
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$this->load->helper(array('form'));
		//echo $this->factura->getConsecutivo($data['Sucursal_Codigo']);
		//date_default_timezone_set("America/Costa_Rica");
		//echo date("y/m/d : H:i:s", now());
		$conf_array = $this->XMLParser->getConfigArray();
		$data['c_array'] = $conf_array;
		$this->load->view('facturas/view_nueva_factura', $data);	
	}
	
	function getNombreCliente()
	{
		//En realidad no devuelve solo el nombre, sino devuelve mas atributos
		if(isset($_GET['cedula'])){ //Si existe el parametro
			$id_request=$_GET['cedula'];
			$arrayCliente = $this->cliente->getNombreCliente($id_request);
			if($arrayCliente){ //Si encontro al cliente
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
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
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
				include '/../get_session_data.php';				
				if($this->articulo->existe_Articulo($codigo_articulo,$data['Sucursal_Codigo'])){
					if($articulo = $this->articulo->getArticuloArray($codigo_articulo, $cedula, $data['Sucursal_Codigo'])){
						if($articulo['inventario'] > 0){
							$retorno['status'] = 'success';
							$retorno['articulo'] = $articulo;
							unset($retorno['error']);
						}else{
							$retorno['error'] = '6'; //No tiene inventario
						}
					}else{
						$retorno['error'] = '5'; //No existe articulo
					}
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
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		date_default_timezone_set("America/Costa_Rica");
	    $Current_datetime = date("y/m/d : H:i:s", now());
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
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
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
					echo $this->articulo->actualizarInventarioRESTA($codigo_articulo, $operacionARRAY[1], $data['Sucursal_Codigo']);
				}
			}
			else if($operacionARRAY[0]=='2'){ //Se suma al inventario
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
		$contraseña=$_GET['pass'];
		$tipo=$_GET['tipo'];
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		
		if($this->user->isAdministrador($usuario, $contraseña)){
			echo '200'; //Si se encontro	
			$codigo_usuario = $this->user->getIdFromUserID($usuario, $data['Sucursal_Codigo']);
			if($tipo=='1'){ //Autorizo un articulo generico
				$this->user->guardar_transaccion($codigo_usuario, "$usuario autorizo articulo generico, sesion de: ".$data['Usuario_Codigo'], $data['Sucursal_Codigo'],'autoriza');
			}elseif($tipo=='2'){  //Autorizo descuento
				$this->user->guardar_transaccion($codigo_usuario, "$usuario autorizo un descuento, sesion de: ".$data['Usuario_Codigo'], $data['Sucursal_Codigo'],'autoriza');
			}
		}
		else{
			echo '-1'; //No se encontro
		}
	}
	
	function crearPendiente(){
		if(isset($_POST['head'])&&isset($_POST['items'])){
		    //Obtener las dos partes del post
			$info_factura = $_POST['head'];
			$items_factura = $_POST['items'];
			//Decodificar el JSON del post
			$info_factura = json_decode($info_factura, true);			
			$items_factura = json_decode($items_factura, true);
			//Obtenemos la primera posicion del info_factura para obtener el array final
			$info_factura = $info_factura[0];
						
			include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
			if($consecutivo = $this->factura->crearfactura($info_factura['ce'], $info_factura['no'], $info_factura['cu'], $info_factura['ob'], $data['Sucursal_Codigo'], $data['Usuario_Codigo'], false)){
				$this->agregarItemsFactura($items_factura, $consecutivo, $data['Sucursal_Codigo'], $data['Usuario_Codigo'], $info_factura['ce']); //Agregamos los items				
				$this->actualizarCostosFactura($consecutivo, $data['Sucursal_Codigo']);
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ".$data['Usuario_Codigo']." envio a caja la factura consecutivo:$consecutivo", $data['Sucursal_Codigo'],'factura_envio');
				echo '7'; //El ingreso fue correcto											
			}else{
				echo '11'; //Error al crear la factura
			}			
		}
		else{echo '10';} //Numero de error mal post		
	}
	
	function agregarItemsFactura($items_factura, $consecutivo, $sucursal, $vendedor, $cliente){
		
		foreach($items_factura as $item){
		//{co:codigo, de:descripcion, ca:cantidad, ds:descuento, pu:precio_unitario, ex:exento}
			if($item['co']=='00'){ //Si es generico					
					$this->factura->addItemtoInvoice($item['co'], $item['de'], $item['ca'], $item['ds'], $item['ex'], $item['pu'], $consecutivo, $sucursal, $vendedor, $cliente,'');
			}else{ //Si es normal					
				if($this->articulo->existe_Articulo($item['co'], $sucursal)){ //Verificamos que el codigo exista
					//Obtenemos los datos que no vienen en el JSON
					$descripcion = $this->articulo->getArticuloDescripcion($item['co'], $sucursal);
					$imagen = $this->articulo->getArticuloImagen($item['co'], $sucursal);
					$precio = $this->articulo->getPrecioProducto($item['co'], $this->articulo->getNumeroPrecio($cliente), $sucursal);
					$this->factura->addItemtoInvoice($item['co'], $descripcion, $item['ca'], $item['ds'], $item['ex'], $precio, $consecutivo, $sucursal, $vendedor, $cliente, $imagen);
				}
			}
			
		}
		//$this->factura->addItemtoInvoice($codigo, $descripcion, $cantidad, $descuento, $exento, $precio, $consecutivo, $sucursal, $vendedor, $cliente);
	}
	
	function actualizarCostosFactura($consecutivo, $sucursal){
		$costosArray = $this->factura->getCostosTotalesFactura($consecutivo, $sucursal);
		$this->factura->updateCostosTotales($costosArray, $consecutivo, $sucursal);
	}
 
}// FIN DE LA CLASE


?>