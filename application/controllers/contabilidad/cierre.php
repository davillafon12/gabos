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
		
		//Esta fecha no va formateada, ya que se formatea cuando se trae la info de la BD
		$fechaUltimoCierra = $this->contabilidad->getFechaUltimoCierreCaja($data['Sucursal_Codigo']);
		$fechaHoraActual = date("Y-m-d  H:i:s", now()); //PARA USAR CON LA BASE DE DATOS
		
		$data['fechaActual'] = date('d-m-Y', now()); // PARA IMPRIMIR
		$data['fechaRealActual'] = $fechaHoraActual; //Fecha que se manda a vista para procesar despues
		$data['baseCaja'] = "30000";
		$data['tipo_cambio'] = $this->configuracion->getTipoCambioCompraDolar();
		
		$facturas = $this->getPrimeraUltimaFactura($data['Sucursal_Codigo'], $fechaHoraActual, $fechaUltimoCierra);
		
		$data['primeraFactura'] = $facturas['primera'];
		$data['ultimaFactura'] = $facturas['ultima'];
		
		$retirosParciales = $this->getRetirosParcialesYTotal($data['Sucursal_Codigo'], $fechaHoraActual, $fechaUltimoCierra);
		
		$data['retirosParciales'] = $retirosParciales['retiros'];
		$data['totalRecibosParciales'] = $retirosParciales['total'];
			
		$data['pagoDatafonos'] = $this->getPagosDatafonos($data['Sucursal_Codigo'], $fechaHoraActual, $fechaUltimoCierra);
		
		$data['pagoMixto'] = $this->obtenerPagosMixtos($data['Sucursal_Codigo'], $fechaHoraActual, $fechaUltimoCierra);
		
		$data['recibos'] = $this->obtenerRecibosDeDinero($data['Sucursal_Codigo'], $fechaHoraActual, $fechaUltimoCierra);
		
		$data['totalFacturasContado'] = $this->obtenerTotalFacturasContado($data['Sucursal_Codigo'], $fechaHoraActual, $fechaUltimoCierra);
		
		$data['totalCreditos'] = $this->obtenerTotalCreditos($data['Sucursal_Codigo'], $fechaHoraActual, $fechaUltimoCierra);
		
		$data['totalNotasCredito'] = $this->obtenerTotalesNotasCredito($data['Sucursal_Codigo'], $fechaHoraActual, $fechaUltimoCierra);
		
		$data['detalleNotasCredito'] = $this->contabilidad->getInfoGeneralNotaCreditoPorRangoFecha($data['Sucursal_Codigo'], date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual);
		
		$data['totalNotasDebito'] = $this->obtenerTotalesNotasDebito($data['Sucursal_Codigo'], $fechaHoraActual, $fechaUltimoCierra);
		
		$data['totalFacturasDeposito'] = $this->obtenerTotalFacturasDeposito($data['Sucursal_Codigo'], $fechaHoraActual, $fechaUltimoCierra);
		
		$data['vendedores'] = $this->obtenerVendidoPorCadaVendedor($data['Sucursal_Codigo'], $fechaHoraActual, $fechaUltimoCierra);
		
		$data['valoresFinales'] = $this->obtenerValoresFinales($data['Sucursal_Codigo'], $fechaHoraActual, $fechaUltimoCierra);
		
		$this->load->view('contabilidad/cierre_caja_view', $data);
	}	
	
	function getPrimeraUltimaFactura($sucursal, $fechaHoraActual, $fechaUltimoCierra){				
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
	
	function getRetirosParcialesYTotal($sucursal, $fechaHoraActual, $fechaUltimoCierra){
		$total = 0;
		
		if($retiros = $this->contabilidad->getRetirosParcialesRangoFechas($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
			foreach($retiros as $ret){
				$total = $total + $ret->Monto;
			}
		}
		
		return array("retiros" => $retiros, "total" => $total);
	}
	
	function getPagosDatafonos($sucursal, $fechaHoraActual, $fechaUltimoCierra){
		if(!$bancos = $this->banco->getBancos()){
			return array('datafonos' => array(), 'totalDatafonos' => 0, 'totalComision' => 0, 'totalRetencion' => 0);
		}
			
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
	
	function obtenerPagosMixtos($sucursal, $fechaHoraActual, $fechaUltimoCierra){
		$pagos = $this->contabilidad->getPagosMixtosPorRangoFecha($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual);
		$cantidadFacturas = 0;
		$total = 0;
		$tarjeta = 0;
		$efectivo = 0;			
		if($pagos->num_rows()!=0){
			$cantidadFacturas = $pagos->num_rows();
			$pagos = $pagos->result();			
			foreach($pagos as $pago){
				$total += $pago->monto;
				$tarjeta += $pago->pago_tarjeta;
			}			
			$efectivo = $total - $tarjeta;
		}		
		return array('cantidadFacturas'=>$cantidadFacturas,'total'=>$total,'tarjeta'=>$tarjeta,'efectivo'=>$efectivo);
	}
	
	function obtenerRecibosDeDinero($sucursal, $fechaHoraActual, $fechaUltimoCierra){
		$total = 0;
		$efectivo = 0;
		$tarjeta = 0;
		$deposito = 0;
		$totalAbonoApartado = $this->contabilidad->getAbonoFacturasApartadoPorRangoFecha($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual); //Guarda la cantidad de dinero del abono del apartado
		if($recibos = $this->contabilidad->getRecibosPorRangoFecha($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
			foreach($recibos as $recibo){
				$total += $recibo->Recibo_Cantidad;
				switch($recibo->Tipo_Pago){
					case 'contado':
						$efectivo += $recibo->Recibo_Cantidad;
					break;
					case 'deposito':
						$deposito += $recibo->Recibo_Cantidad;
					break;
					case 'tarjeta':
						$tarjeta += $recibo->Recibo_Cantidad;
					break;
				}
			}
		}
		$total += $totalAbonoApartado;
		return array('total'=>$total, 'efectivo'=>$efectivo, 'tarjeta'=>$tarjeta, 'deposito'=>$deposito, 'abonos'=>$totalAbonoApartado);
	}
	
	function obtenerTotalFacturasContado($sucursal, $fechaHoraActual, $fechaUltimoCierra){
		$total = 0;
		if($facturas = $this->contabilidad->getFacturasContadoPorRangoFecha($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
			foreach($facturas as $factura){
				$total += $factura->Factura_Monto_Total;
			}
		}
		return $total;
	}
	
	
	function obtenerTotalFacturasDeposito($sucursal, $fechaHoraActual, $fechaUltimoCierra){
		$total = 0;
		if($facturas = $this->contabilidad->getFacturasDepositoPorRangoFecha($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
			
			foreach($facturas as $factura){
				$total += $factura->Factura_Monto_Total;
			}
		}
		return $total;
	}
	
	function obtenerValoresFinales($sucursal, $fechaHoraActual, $fechaUltimoCierra){
			$totalFacturas = 0;
			$totalIVA = 0;
			$totalRetencion = 0;
			
			$totalNotasCredito = 0;
			$totalIVANotaCredito = 0;
			if($facturas = $this->contabilidad->getFacturasPorRangoFecha($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
					foreach($facturas as $factura){
						$totalFacturas += $factura->Factura_Monto_Total;
						$totalIVA += $factura->Factura_Monto_IVA;
						$totalRetencion += $factura->Factura_Retencion;
					}
					$notasCredito = $this->obtenerTotalesNotasCredito($sucursal, $fechaHoraActual, $fechaUltimoCierra);
					$totalNotasCredito = $notasCredito['total'];
					$totalIVANotaCredito = $notasCredito['iva'];
			}
			//Le restamos la parte de las notas debito
			$totalFacturas -= $totalNotasCredito;
			$totalIVA -= $totalIVANotaCredito;
			
			return array("totalFacturas"=>$totalFacturas,"totalIVA"=>$totalIVA,"totalRetencion"=>$totalRetencion);
	}
	
	function obtenerTotalCreditos($sucursal, $fechaHoraActual, $fechaUltimoCierra){
		//INCLUYE LOS APARTADOS!!!!!!!
		$totalCredito = 0;
		$totalApartado = 0;
		$totalAbonoApartado = $this->contabilidad->getAbonoFacturasApartadoPorRangoFecha($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual); //Guarda la cantidad de dinero del abono del apartado
		if($facturas = $this->contabilidad->getFacturasCreditoYApartadoPorRangoFecha($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
			foreach($facturas as $factura){
					if($factura->Factura_Tipo_Pago == 'credito'){
							$totalCredito += $factura->Factura_Monto_Total;
					}elseif($factura->Factura_Tipo_Pago == 'apartado'){
							$totalApartado += $factura->Factura_Monto_Total;
					}
			}
		}
		$totalApartado -= $totalAbonoApartado;
		return array('totalCredito'=>$totalCredito, 'totalApartado'=>$totalApartado);
	}
	
	function obtenerTotalesNotasCredito($sucursal, $fechaHoraActual, $fechaUltimoCierra){
		$total = 0;
		$subtotal = 0;
		$total_iva = 0;
		if($notas = $this->contabilidad->getNotaCreditoPorRangoFecha($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
			foreach($notas as $nota){
				if($notaCreditoBody = $this->contabilidad->getArticulosNotaCreditoParaImpresion($nota->Consecutivo, $sucursal)){
					
					foreach($notaCreditoBody as $art){
						$total = $total + ($art->precio * ($art->bueno + $art->defectuoso));
						$total_iva = $total_iva + (($art->precio * ($art->bueno + $art->defectuoso)) * ($nota->Por_IVA/100));
						$subtotal = $subtotal + (($art->precio * ($art->bueno + $art->defectuoso)) - (($art->precio * ($art->bueno + $art->defectuoso)) * ($nota->Por_IVA/100)));
					}
				}
			}
		}
		return array('total'=>$total, 'subtotal'=>$subtotal, 'iva'=>$total_iva);		
	}
	
	function obtenerTotalesNotasDebito($sucursal, $fechaHoraActual, $fechaUltimoCierra){
		$total = 0;
		$subtotal = 0;
		$total_iva = 0;
		if($notas = $this->contabilidad->getNotaDebitoPorRangoFecha($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
			foreach($notas as $nota){
				if($notaDebitoBody = $this->contabilidad->getProductosNotaDebito($nota->Consecutivo, $sucursal)){
					foreach($notaDebitoBody as $art){
						$total += ($art->precio * $art->cantidad);
						$total_iva += (($art->precio * $art->cantidad) * ($nota->Impuesto_Porcentaje/100));
						$subtotal += (($art->precio * $art->cantidad) - (($art->precio * $art->cantidad) * ($nota->Impuesto_Porcentaje/100)));
					}
				}
			}
		}
		return array('total'=>$total, 'subtotal'=>$subtotal, 'iva'=>$total_iva);		
	}
	
	function obtenerVendidoPorCadaVendedor($sucursal, $fechaHoraActual, $fechaUltimoCierra){
				
		$vendidoVendedores = array();
		$totalVendido = 0;
		
		if($vendedores = $this->user->getVendedores($sucursal)){
			foreach($vendedores as $vendedor){
				if($vendido = $this->contabilidad->getVendidoPorVendedor($vendedor->Factura_Vendedor_Codigo, $sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
					array_push($vendidoVendedores, $vendido);
					$totalVendido += $vendido[0]->total_vendido;
				}
			}
		}
		
		return array('vendidoVendedores'=>$vendidoVendedores,'totalVendido'=>$totalVendido);
	}
	
	function crearCierre(){
			//'cantidadEfectivo':cantidad, 'tipo_cambio':tipo_cambio, 'colones':getJSONColones(), 'dolares':getJSONDolares(), 'fechaCierre':fechaReal	
			$retorno['status'] = 'error';
			$retorno['error'] = '1'; //No se proceso la solicitud
			if(isset($_POST['cantidadEfectivo'])&&isset($_POST['tipo_cambio'])&&isset($_POST['colones'])&&isset($_POST['dolares'])&&isset($_POST['fechaCierre'])&&isset($_POST['base'])&&isset($_POST['bnservicios'])){
				$cantidad = trim($_POST['cantidadEfectivo']);
				$cantidad = str_replace(".","",$cantidad);
				$cantidad = str_replace(",",".",$cantidad);
				$base = trim($_POST['base']);
				$base = str_replace(".","",$base);
				$base = str_replace(",",".",$base);
				$tipo_cambio = $_POST['tipo_cambio'];
				$bnServicios = trim($_POST['bnservicios']);
				if(is_numeric($cantidad)&&is_numeric($tipo_cambio)&&is_numeric($base)&&is_numeric($bnServicios)){
					$colones = json_decode($_POST['colones']);
					$dolares = json_decode($_POST['dolares']);
					include '/../get_session_data.php';
					if(!$this->factura->getFacturasPendientes($data['Sucursal_Codigo'])){
							
							$cierre = $this->contabilidad->crearCierreCaja($tipo_cambio, $cantidad, $base, $_POST['fechaCierre'], $data['Sucursal_Codigo'], $data['Usuario_Codigo'], $bnServicios);
							
							foreach($colones as $colon){
								$tipo = 'moneda';
								if($colon->denominacion>500){
									$tipo = 'billete';						
								}
								$this->contabilidad->agregarDenominacionCierreCaja($colon->denominacion, $colon->cantidad, $tipo, 'colones', $cierre);					
							}
							
							foreach($dolares as $dolar){
								$this->contabilidad->agregarDenominacionCierreCaja($dolar->denominacion, $dolar->cantidad, 'billete', 'dolares', $cierre);					
							}
							
							
							$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario realizo un cierre de caja #$cierre Fecha: ".$_POST['fechaCierre'],$data['Sucursal_Codigo'],'cierre_caja');
							
							
							$retorno['status'] = 'success';
							unset($retorno['error']);
							$retorno['cierre'] = $cierre;
							$retorno['sucursal']= $data['Sucursal_Codigo'];
							$retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
							$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");
					}else{
						$retorno['error'] = '4'; //Aun hay facturas pendientes
					}					
				}else{
					$retorno['error'] = '3'; //Cantidad no valida
				}
			}else{
				$retorno['error'] = '2'; //URL mala
			}
			echo json_encode($retorno);	
	}
	
}

?>