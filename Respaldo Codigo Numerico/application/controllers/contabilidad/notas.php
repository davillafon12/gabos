<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class notas extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('cliente','',TRUE);
		$this->load->model('contabilidad','',TRUE);	
		$this->load->model('factura','',TRUE);
		$this->load->model('articulo','',TRUE);
		$this->load->model('XMLParser','',TRUE);
	}

	function index()
	{
		redirect('home', 'location');		
	}
	
	function notasCredito(){
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if($permisos['entrar_notas'])
		{
			$this->load->view('contabilidad/notas_credito_view', $data);			
		}
		else{
			redirect('accesoDenegado', 'location');
		}
	
	}
	
	function getFacturasCliente(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['cedula'])){
			$cedula = $_POST['cedula']; 
			if(trim($cedula) == '1' || trim($cedula) == '0'){
				$retorno['error'] = '4'; //Error cliente contado y afiliado
			}else{
				if($clienteArray = $this->cliente->getClientes_Cedula($cedula)){
					include '/../get_session_data.php';					
					if($facturas = $this->cliente->getFacturasDeClienteEnSucursal($cedula, $data['Sucursal_Codigo'])){
						$facturasDevolver = array();
						foreach($facturas as $factura){
							$aux['consecutivo'] = $factura->Factura_Consecutivo;
							$fecha = date('d-m-Y', strtotime($factura->Factura_Fecha_Hora));
							$aux['fecha'] = $fecha;
							$aux['monto'] = $factura->Factura_Monto_Total;
							array_push($facturasDevolver, $aux);
						}
						
						foreach($clienteArray as $row){
							$cliente['nombre'] = $row-> Cliente_Nombre;
							$cliente['apellidos'] = $row-> Cliente_Apellidos;						
						}
						
						$retorno['status'] = 'success';
						$retorno['facturas'] = $facturasDevolver;
						$retorno['cliente'] = $cliente;
					}else{
						$retorno['error'] = '5'; //Error no tiene facturas pendientes
					}					
				}else{
					$retorno['error'] = '3'; //Error no hay cliente
				}
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);	
	}
	
	function getProductosDeFactura(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['factura'])){
			$factura = $_POST['factura']; 
			include '/../get_session_data.php';
			
			if($productosFactura = $this->factura->getArticulosFactura($factura, $data['Sucursal_Codigo'])){
				$productosDevolver = array();
				foreach($productosFactura as $producto){
					$aux['codigo'] = $producto->Articulo_Factura_Codigo;
					$aux['descripcion'] = $producto->Articulo_Factura_Descripcion;
					$aux['cantidad'] = $producto->Articulo_Factura_Cantidad;
					array_push($productosDevolver, $aux);
				}
				
				$retorno['status'] = 'success';
				$retorno['productos'] = $productosDevolver;
				
			}else{
				$retorno['error'] = '6'; //Error no hay cliente
			}			
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);	
	}
	
	function getFacturasFiltradasCodigo(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['cedula'])&&isset($_POST['codigo'])){
			$cedula = $_POST['cedula']; 
			$codigo = $_POST['codigo']; 
			if(trim($cedula) == '1' || trim($cedula) == '0'){
				$retorno['error'] = '4'; //Error cliente contado y afiliado
			}else{
				if($clienteArray = $this->cliente->getClientes_Cedula($cedula)){
					include '/../get_session_data.php';					
					if($facturas = $this->cliente->getFacturasDeClienteEnSucursalFiltradasCodigo($cedula, $data['Sucursal_Codigo'], $codigo)){
						$facturasDevolver = array();
						foreach($facturas as $factura){
							$aux['consecutivo'] = $factura->Factura_Consecutivo;
							$fecha = date('d-m-Y', strtotime($factura->Factura_Fecha_Hora));
							$aux['fecha'] = $fecha;
							$aux['monto'] = $factura->Factura_Monto_Total;
							array_push($facturasDevolver, $aux);
						}
						
						$retorno['status'] = 'success';
						$retorno['facturas'] = $facturasDevolver;
					}else{
						$retorno['status'] = 'success';
						$retorno['facturas'] = array();
					}					
				}else{
					$retorno['error'] = '3'; //Error no hay cliente
				}
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);	
	}
	
	function consecutivoFacturaExiste(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['cedula'])&&isset($_POST['consecutivo'])){
			$cedula = $_POST['cedula']; 
			$consecutivo = $_POST['consecutivo']; 
			if(trim($cedula) == '1' || trim($cedula) == '0'){
				$retorno['error'] = '4'; //Error cliente contado y afiliado
			}else{
				if($clienteArray = $this->cliente->getClientes_Cedula($cedula)){
					include '/../get_session_data.php';					
					if($factura = $this->cliente->getFacturaDeClienteCobrada($consecutivo, $data['Sucursal_Codigo'], $cedula)){
						$retorno['status'] = 'success';
					}else{
						$retorno['error'] = '10';
					}					
				}else{
					$retorno['error'] = '3'; //Error no hay cliente
				}
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);	
	}
	
	function generarNota(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		//Revisamos que vengas las variables necesarias
		if(	isset($_POST['cedula'])
			&&isset($_POST['nombre'])
			&&isset($_POST['facturaAplicar'])
			&&isset($_POST['facturaSeleccion'])
			&&isset($_POST['productos'])){
			//Obtenemos los productos
			$productosAAcreditar = json_decode($_POST['productos']);	
			//Verificamos que no vengan vacios
			if(trim($_POST['cedula'])!=''
				&&trim($_POST['nombre'])!=''
				&&trim($_POST['facturaAplicar'])!=''
				&&trim($_POST['facturaSeleccion'])!=''
				&&sizeOf($productosAAcreditar)>0){
				//Cargamos las variables
				$cedula = $_POST['cedula'];
				$nombre = $_POST['nombre'];
				$facturaAplicar = $_POST['facturaAplicar'];
				$facturaAcreditar = $_POST['facturaSeleccion'];
				//Verificamos que exista cliente
				if(is_numeric($cedula)&&$this->cliente->existe_Cliente($cedula)){
					include '/../get_session_data.php';	
					//Verificamos que existan las facturas
					if(is_numeric($facturaAplicar)&&$this->factura->existe_Factura($facturaAplicar, $data['Sucursal_Codigo'])
						&&is_numeric($facturaAcreditar)&&$this->factura->existe_Factura($facturaAcreditar, $data['Sucursal_Codigo'])){
						if($this->existeProductosAcreditar($productosAAcreditar, $data['Sucursal_Codigo'])){
							//Preguntamos si la factura a aplicar ya fue aplicada en otra nota
							if(!$this->contabilidad->facturaAplciarYaFueAplicada($facturaAplicar, $data['Sucursal_Codigo'])){
								//Listo para realizar nota
								//Obtenemos el consecutivo
								if($consecutivo = $this->contabilidad->getConsecutivo($data['Sucursal_Codigo'])){
									date_default_timezone_set("America/Costa_Rica");
									$fecha = date("y/m/d : H:i:s", now());
								
									if($this->contabilidad->agregarNotaCreditoCabecera($consecutivo, $fecha, $nombre, $cedula, $data['Sucursal_Codigo'], $facturaAcreditar, $facturaAplicar)){
										$this->contabilidad->agregarProductosNotaCredito($consecutivo, $data['Sucursal_Codigo'], $productosAAcreditar, $cedula);
										
										$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario realizo la nota credito: $consecutivo",$data['Sucursal_Codigo'],'nota');
										
										$retorno['status'] = 'success';
										$retorno['consecutivo'] = $consecutivo;
										unset($retorno['error']);
									}else{
										//No se pudo crear la nota
										$retorno['error'] = '9';
									}
								}else{
									//No se pudo obtener el nuevo consecutivo
									$retorno['error'] = '8';
								}
							}else{
								//La factura a aplicar ya fue aplicada
								$retorno['error'] = '7';
							}
						}else{
							//Algun producto ya no existe
							$retorno['error'] = '6';
						}
					}else{
						//Alguna factura no es valida o no existe
						$retorno['error'] = '5';
					}
				}else{
					//Cliente no valido
					$retorno['error'] = '4'; 
				}			
			}else{
				//Campos Vacios
				$retorno['error'] = '3';
			}			
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);
	}
	
	private function existeProductosAcreditar($productos, $sucursal){
		foreach($productos as $producto){
			if(!$this->articulo->existe_Articulo($producto->c,$sucursal)){return false;}
		}
		return true;
	}
	
	function notasDebito(){
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if($permisos['entrar_notas_d'])
		{
			$this->load->view('contabilidad/notas_debito_view', $data);			
		}
		else{
			redirect('accesoDenegado', 'location');
		}
	}
	
	function generarNotaDebito(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['productos'])){
			if(sizeOf($_POST['productos'])!=0)
			{
				$productos = json_decode($_POST['productos']);
				//var_dump($productos);
				include '/../get_session_data.php';
				
				$consecutivo = $this->getNextConsecutivoNotaDebito($data['Sucursal_Codigo']);
				date_default_timezone_set("America/Costa_Rica");
				$fecha = date("y/m/d : H:i:s", now());
				$c_array = $this->XMLParser->getConfigArray();				
				
				$this->contabilidad->crearNotaDebito($consecutivo, $fecha, $c_array['iva'], $data['Usuario_Codigo'], $data['Sucursal_Codigo']);
				
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario realizo la nota debito: $consecutivo",$data['Sucursal_Codigo'],'nota');
				
				foreach($productos as $producto){
					if($this->articulo->existe_Articulo($producto->co,$data['Sucursal_Codigo'])){
						if(is_numeric($producto->ca)&&$producto->ca>0){
							//Sacamos el producto de inventario, la funcion esta valida que la cantidad no sea mayor al inventario actual
							if($this->articulo->actualizarInventarioRESTA($producto->co, $producto->ca, $data['Sucursal_Codigo'])=='3'){
								$descripcion = $this->articulo->getArticuloDescripcion($producto->co, $data['Sucursal_Codigo']);
								$costo = $this->articulo->getPrecioProducto($producto->co, 0, $data['Sucursal_Codigo']);
							
								$this->contabilidad->agregarArticuloNotaDebito($producto->co, $descripcion, $producto->ca, $costo, $consecutivo, $data['Sucursal_Codigo'], $data['Usuario_Codigo']);
							}
						}
						//Si la cantidad no es numerica o mayor a 0
					}
					//Si no existe el articulo no lo procesa
				}
				$retorno['status'] = 'success';
				$retorno['consecutivo'] = $consecutivo;
				unset($retorno['error']);
			}else{
				$retorno['error'] = '3'; //No vienen productos
			}
		}else{
			$retorno['error'] = '2'; //URL mala
		}
		echo json_encode($retorno);
	}
	
	function getNextConsecutivoNotaDebito($sucursal){
		return $this->contabilidad->getConsecutivoUltimaNotaDebito($sucursal)+1;
	}
}

?>