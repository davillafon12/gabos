<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class consultaVenta extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);
		$this->load->model('contabilidad','',TRUE);
	}

	function index()
	{
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if($permisos['consultar_ventas'])
		{
			$empresas_actuales = $this->empresa->get_empresas_ids_array();
			$data['Familia_Empresas'] = $empresas_actuales;
			$this->load->view('contabilidad/consultar_ventas_view', $data);			
		}
		else{
			redirect('accesoDenegado', 'location');
		}			
	}
	
	function getDatosConsultaVenta(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['sucursal'])){
			$sucursal = $_POST['sucursal'];
			if($this->empresa->getEmpresa($sucursal)){
				date_default_timezone_set("America/Costa_Rica");
				
				$fechaUltimoCierra = $this->contabilidad->getFechaUltimoCierreCaja($sucursal);
				$fechaHoraActual = date("Y-m-d  H:i:s", now());
				
				$primeraFactura = 0;
				$ultimaFactura = 0;
				$ventaTotal = 0;
				
				if($facturas = $this->contabilidad->getFacturasEntreRangoFechas($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){					
					$contador = 1;
					foreach($facturas as $factura){
						if($contador == 1){
							//Si es la primera factura la metemos en la variable de la primera
							$primeraFactura = $factura->Factura_Consecutivo;
							$contador++;
						}
						//Siempre actualizara el valor hasta llegar al final
						$ultimaFactura = $factura->Factura_Consecutivo;
						
						//Realizamos la sumatoria de total
						$ventaTotal += $factura->Factura_Monto_Total;
					}
				}
				
				unset($retorno['error']);
				$retorno['status'] = 'success';
				$retorno['primeraFactura'] = $primeraFactura;
				$retorno['ultimaFactura'] = $ultimaFactura;
				$retorno['fecha'] = $fechaHoraActual;
				$retorno['fecha_cierre'] = date('Y-m-d H:i:s', $fechaUltimoCierra);
				$retorno['venta'] = $ventaTotal;
			}else{
				$retorno['error'] = '3'; //No existe Sucursal
			}
		}else{
			$retorno['error'] = '2'; //URL MALA
		}
		echo json_encode($retorno);	
	}
	
	
	
}

?>