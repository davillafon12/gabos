<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cierre extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('contabilidad','',TRUE);
		$this->load->model('banco','',TRUE);
		$this->load->model('factura','',TRUE);
		$this->load->model('configuracion','',TRUE);
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
			
		$data['pagoDatafonos'] = $this->getPagosDatafonos($data['Sucursal_Codigo']);
		
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
			return array('datafonos' => array(), 'totalDatafonos' => 0, 'totalComision' => 0, 'totalRetencion' => 0);
		}
		
		$fechaUltimoCierra = $this->contabilidad->getFechaUltimoCierreCaja($sucursal);
				
		date_default_timezone_set("America/Costa_Rica");
		$fechaHoraActual = date("Y-m-d : H:i:s", now());
		
		$totalPagosTarjeta = 0;
		$totalComisionFinal = 0;
		$totalRetencionFinal = 0;
		$procentajeRetencion = $this->configuracion->getPorRetencionHaciendaTarjeta();
		
		for($count = 0; $count < sizeOf($bancos); $count++){
			$totalComision = 0;
			$totalRetencion = 0;
			$total = 0;
						
			//Pagos con tarjeta en facturas
			if($facturasTarjeta = $this->contabilidad->getFacturasPagasTarjetaRangoFechasYBanco($sucursal, $bancos[$count]->Banco_Codigo, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
				foreach($facturasTarjeta as $fac){
					if($fac->Factura_Tipo_Pago == 'tarjeta'){
						$totalPagado = $this->factura->getMontoTotalPago($sucursal, $fac->Factura_Consecutivo);
						$porcentajeComision = $fac->Tarjeta_Comision_Banco;						
					}else if($fac->Factura_Tipo_Pago == 'mixto'){
						$totalPagado = $this->factura->getMontoPagoTarjetaMixto($sucursal, $fac->Factura_Consecutivo);
						$porcentajeComision = $fac->Tarjeta_Comision_Banco;
					}					
					$totalComision = $totalComision + ($totalPagado * ($porcentajeComision/100));
					$totalRetencion = $totalRetencion + ($totalPagado * ($procentajeRetencion/100));
					$total = $total + $totalPagado;
				}				
			}
			
			//Pagos con tarjeta en recibos
			if($recibos = $this->contabilidad->getRecibosPagadosConTarjetaRangoFecha($sucursal, $bancos[$count]->Banco_Codigo, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
				foreach($recibos as $recibo){
					$totalPagado = $recibo->Recibo_Cantidad;
					$porcentajeComision = $recibo->Comision_Por;
					$totalComision = $totalComision + ($totalPagado * ($porcentajeComision/100));	
					$totalRetencion = $totalRetencion + ($totalPagado * ($procentajeRetencion/100));
					$total = $total + $totalPagado;
				}
			}
			
			
			$totalPagosTarjeta = $totalPagosTarjeta + $total;
			$totalComisionFinal = $totalComisionFinal + $totalComision;
			$totalRetencionFinal = $totalRetencionFinal + $totalRetencion;
			$bancos[$count]->Total_Comision = $totalComision;
			$bancos[$count]->Total_Retencion = $totalRetencion;
			$bancos[$count]->Total = $total;					
		}
		return array('datafonos' => $bancos, 'totalDatafonos' => $totalPagosTarjeta, 'totalComision' => $totalComisionFinal, 'totalRetencion' => $totalRetencionFinal);
	}
	
}

?>