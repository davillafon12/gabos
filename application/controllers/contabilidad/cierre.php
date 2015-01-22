<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cierre extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('contabilidad','',TRUE);
		$this->load->model('banco','',TRUE);
		$this->load->model('factura','',TRUE);
	}

	function index()
	{
		redirect('home', 'location');		
	}
	
	function caja(){
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['cierre_caja'])
		{
			redirect('accesoDenegado', 'location');						
		}
		
		date_default_timezone_set("America/Costa_Rica");
		$data['fechaActual'] = date('d-m-Y', now());
		$data['baseCaja'] = "₡ 30.000";
		
		$facturas = $this->getPrimeraUltimaFactura($data['Sucursal_Codigo']);
		
		$data['primeraFactura'] = $facturas['primera'];
		$data['ultimaFactura'] = $facturas['ultima'];
		
		$retirosParciales = $this->getRetirosParcialesYTotal($data['Sucursal_Codigo']);
		
		$data['retirosParciales'] = $retirosParciales['retiros'];
		$data['totalRecibosParciales'] = $retirosParciales['total'];
		
		$pagosDatafonos = $this->getPagosDatafonos($data['Sucursal_Codigo']);
		
		$data['datafonos'] = $pagosDatafonos['datafonos']; 
		$data['totalDatafonos'] = $pagosDatafonos['totalDatafonos'];
		
		$this->load->view('contabilidad/cierre_caja_view', $data);
	}	
	
	function getPrimeraUltimaFactura($sucursal){
		$fechaUltimoCierra = $this->contabilidad->getFechaUltimoCierreCaja($sucursal);
				
		date_default_timezone_set("America/Costa_Rica");
		$fechaHoraActual = date("Y-m-d : H:i:s", now());
		
		$primeraFactura = 0;
		$ultimaFactura = 0;
		
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
			}
		}
		return array("primera"=>$primeraFactura, "ultima"=>$ultimaFactura);
	}
	
	function getRetirosParcialesYTotal($sucursal){
		$fechaUltimoCierra = $this->contabilidad->getFechaUltimoCierreCaja($sucursal);
				
		date_default_timezone_set("America/Costa_Rica");
		$fechaHoraActual = date("Y-m-d : H:i:s", now());
		
		
		$total = 0;
		
		if($retiros = $this->contabilidad->getRetirosParcialesRangoFechas($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
			foreach($retiros as $ret){
				$total = $total + $ret->Monto;
			}
		}
		
		return array("retiros" => $retiros, "total" => $total);
	}
	
	function getPagosDatafonos($sucursal){
		if(!$bancos = $this->banco->getBancos()){
			return array('datafonos' => array(), 'totalDatafonos' => 0);
		}
		
		$fechaUltimoCierra = $this->contabilidad->getFechaUltimoCierreCaja($sucursal);
				
		date_default_timezone_set("America/Costa_Rica");
		$fechaHoraActual = date("Y-m-d : H:i:s", now());
		
		$totalPagosTarjeta = 0;
		
		for($count = 0; $count < sizeOf($bancos); $count++){
			if($facturasTarjeta = $this->contabilidad->getFacturasPagasTarjetaRangoFechasYBanco($sucursal, $bancos[$count]->Banco_Codigo, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
				$totalComision = 0;
				$total = 0;
				
				foreach($facturasTarjeta as $fac){
					if($fac->Factura_Tipo_Pago == 'tarjeta'){
						$totalPagado = $this->factura->getMontoTotalPago($sucursal, $fac->Factura_Consecutivo);
						$porcentajeComision = $fac->Tarjeta_Comision_Banco;						
					}else if($fac->Factura_Tipo_Pago == 'mixto'){
						$totalPagado = $this->factura->getMontoPagoTarjetaMixto($sucursal, $fac->Factura_Consecutivo);
						$porcentajeComision = $fac->Tarjeta_Comision_Banco;
					}					
					$totalComision = $totalComision + ($totalPagado * ($porcentajeComision/100));						
					$total = $total + $totalPagado;
				}
				$bancos[$count]->Total_Comision = $totalComision;
				$bancos[$count]->Total = $total;
				$totalPagosTarjeta = $totalPagosTarjeta + $total;
			}else{
				$bancos[$count]->Total_Comision = 0;
				$bancos[$count]->Total = 0;
			}		
		}
		return array('datafonos' => $bancos, 'totalDatafonos' => $totalPagosTarjeta);
	}
	
}

?>