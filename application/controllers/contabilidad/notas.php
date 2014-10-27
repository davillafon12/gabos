<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class notas extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('cliente','',TRUE);
		$this->load->model('contabilidad','',TRUE);	
		$this->load->model('factura','',TRUE);
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
}

?>