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
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['crear_proforma'])
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
		$conf_array = $this->configuracion->getConfiguracionArray();
		$data['c_array'] = $conf_array;
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
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
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
			
			if($consecutivo = $this->proforma_m->crearProforma($info_factura['ce'], $info_factura['no'], $info_factura['cu'], $info_factura['ob'], $data['Sucursal_Codigo'], $data['Usuario_Codigo'])){
				$this->agregarItemsProforma($items_factura, $consecutivo, $data['Sucursal_Codigo'], $data['Usuario_Codigo'], $info_factura['ce']); //Agregamos los items				
				$this->actualizarCostosProforma($consecutivo, $data['Sucursal_Codigo']);
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ".$data['Usuario_Codigo']." creo la proforma consecutivo:$consecutivo", $data['Sucursal_Codigo'],'factura_envio');
				echo '7'; //El ingreso fue correcto											
			}else{
				echo '11'; //Error al crear la factura
			}			
		}
		else{echo '10';} //Numero de error mal post		
	}
	
	function agregarItemsProforma($items_factura, $consecutivo, $sucursal, $vendedor, $cliente){
		
		foreach($items_factura as $item){
		//{co:codigo, de:descripcion, ca:cantidad, ds:descuento, pu:precio_unitario, ex:exento}
			if($item['co']=='00'){ //Si es generico					
					$this->proforma_m->addItemtoInvoice($item['co'], $item['de'], $item['ca'], $item['ds'], $item['ex'], $item['pu'], $consecutivo, $sucursal, $vendedor, $cliente, '');
			}else{ //Si es normal					
				if($this->articulo->existe_Articulo($item['co'], $sucursal)){ //Verificamos que el codigo exista
					//Obtenemos los datos que no vienen en el JSON
					$descripcion = $this->articulo->getArticuloDescripcion($item['co'], $sucursal);
					$imagen = $this->articulo->getArticuloImagen($item['co'], $sucursal);
					$precio = $this->articulo->getPrecioProducto($item['co'], $this->articulo->getNumeroPrecio($cliente), $sucursal);
					$this->proforma_m->addItemtoInvoice($item['co'], $descripcion, $item['ca'], $item['ds'], $item['ex'], $precio, $consecutivo, $sucursal, $vendedor, $cliente, $imagen);
				}
			}
			
		}
		//$this->factura->addItemtoInvoice($codigo, $descripcion, $cantidad, $descuento, $exento, $precio, $consecutivo, $sucursal, $vendedor, $cliente);
	}
	
	function actualizarCostosProforma($consecutivo, $sucursal){
		$costosArray = $this->proforma_m->getCostosTotalesProforma($consecutivo, $sucursal);
		$this->proforma_m->updateCostosTotales($costosArray, $consecutivo, $sucursal);
	}	
 
}// FIN DE LA CLASE


?>