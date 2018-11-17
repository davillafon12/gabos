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
		$this->load->model('configuracion','',TRUE);
		$this->load->model('empresa','',TRUE);
                $this->load->model('impresion_m','',TRUE);
	}

	function index()
	{
		redirect('home', 'location');		
	}
	
	function notasCredito(){
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if($permisos['entrar_notas'])
		{
			$data['javascript_cache_version'] = $this->javascriptCacheVersion;
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
			/*if(trim($cedula) == '1' || trim($cedula) == '0'){
				$retorno['error'] = '4'; //Error cliente contado y afiliado
			}else{*/
				if($clienteArray = $this->cliente->getClientes_Cedula($cedula)){
					include PATH_USER_DATA;					
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
			//}
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
			include PATH_USER_DATA;
			
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
			/*if(trim($cedula) == '1' || trim($cedula) == '0'){
				$retorno['error'] = '4'; //Error cliente contado y afiliado
			}else{*/
				if($clienteArray = $this->cliente->getClientes_Cedula($cedula)){
					include PATH_USER_DATA;					
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
			//}
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
			/*if(trim($cedula) == '1' || trim($cedula) == '0'){
				$retorno['error'] = '4'; //Error cliente contado y afiliado
			}else{*/
				if($clienteArray = $this->cliente->getClientes_Cedula($cedula)){
					include PATH_USER_DATA;					
					if($factura = $this->cliente->getFacturaDeClienteCobrada($consecutivo, $data['Sucursal_Codigo'], $cedula)){
						$retorno['status'] = 'success';
					}else{
						$retorno['error'] = '10';
					}					
				}else{
					$retorno['error'] = '3'; //Error no hay cliente
				}
			//}
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
                                include PATH_USER_DATA;
				//Verificamos que exista cliente
				$this->contabilidad->crearNotaCreditoMacro($retorno, $cedula, $facturaAcreditar, $facturaAplicar, $data['Sucursal_Codigo'], $productosAAcreditar, $data['Usuario_Codigo'], CORRIGE_FACTURA, "Devolucion de mercancia");
                                require_once PATH_API_HACIENDA;
                                $api = new API_FE();
                                $api->destruirSesion($retorno["empresa"]->Ambiente_Tributa, $retorno["empresa"]->Usuario_Tributa);
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
	
	
	
	function notasDebito(){
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if($permisos['entrar_notas_d'])
		{
			$data['Familia_Empresas'] = $this->empresa->get_empresas_ids_array();
                        $data['javascript_cache_version'] = $this->javascriptCacheVersion;
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
				include PATH_USER_DATA;
				
				$consecutivo = $this->getNextConsecutivoNotaDebito($data['Sucursal_Codigo']);
				date_default_timezone_set("America/Costa_Rica");
				$fecha = date("y/m/d : H:i:s", now());
				$c_array = $this->configuracion->getConfiguracionArray();				
				
				$this->contabilidad->crearNotaDebito($consecutivo, $fecha, $c_array['iva'], $data['Usuario_Codigo'], $data['Sucursal_Codigo'], trim($_POST['sucursalRecibe']), trim($_POST['sucursalEntrega']));
				
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario realizo la nota debito: $consecutivo",$data['Sucursal_Codigo'],'nota');
				
				foreach($productos as $producto){
					if($this->articulo->existe_Articulo(trim($producto->co),$data['Sucursal_Codigo'])){
						if(is_numeric($producto->ca)&&$producto->ca>0){
							//Sacamos el producto de inventario, la funcion esta valida que la cantidad no sea mayor al inventario actual
							if($this->articulo->actualizarInventarioRESTA(trim($producto->co), $producto->ca, $data['Sucursal_Codigo'])=='3'){
								$descripcion = $this->articulo->getArticuloDescripcion(trim($producto->co), $data['Sucursal_Codigo']);
								$costo = $this->articulo->getPrecioProducto(trim($producto->co), 0, $data['Sucursal_Codigo']);
							
								$this->contabilidad->agregarArticuloNotaDebito(trim($producto->co), $descripcion, $producto->ca, $costo, $consecutivo, $data['Sucursal_Codigo'], $data['Usuario_Codigo']);
							}
						}
						//Si la cantidad no es numerica o mayor a 0
					}
					//Si no existe el articulo no lo procesa
				}
				
				$retorno['sucursal']= $data['Sucursal_Codigo'];
				$retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
				$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");				
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