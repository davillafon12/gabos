<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class consulta extends CI_Controller {	
	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('banco','',TRUE);
		$this->load->model('factura','',TRUE);
		$this->load->model('contabilidad','',TRUE);
		$this->load->model('proforma_m','',TRUE);
		$this->load->model('empresa','',TRUE);
		$this->load->model('cliente','',TRUE);
		$this->load->model('articulo','',TRUE);
                $this->load->model('impresion_m','',TRUE);
		include 'get_session_data.php';
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		
		if(!$permisos['realizar_consulta'])
		{	
			redirect('accesoDenegado', 'location');	
		}
	}

	function index()
	{
		redirect('home', 'refresh');	
	} 
	
	function facturas(){
		include 'get_session_data.php';
		$data['javascript_cache_version'] = $this->javascriptCacheVersion;
		$this->load->view('consulta/facturas_consulta_view', $data);
	}
	
	function getFacturasFiltradas(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['cliente'])&&isset($_POST['desde'])&&isset($_POST['hasta'])&&isset($_POST['tipo'])&&isset($_POST['estado'])){
			$cliente = $_POST['cliente'];
			$desde = $_POST['desde'];
			$hasta = $_POST['hasta'];
			$tipo = $this->convertirArray($_POST['tipo']);
			$estado = $this->convertirArray($_POST['estado']);
			if($this->verificarFecha($desde)&&$this->verificarFecha($hasta)){				
				include 'get_session_data.php';
				if($facturas = $this->factura->getFacturasFiltradas($cliente, $desde, $hasta, $tipo, $estado, $data['Sucursal_Codigo'])){
					unset($retorno['error']);
					$retorno['status'] = 'success';
					$retorno['facturas'] = $facturas;
				}else{
					//No hay facturas
					$retorno['error'] = '3';
				}				
			}else{
				//Fechas con mal formato
				$retorno['error'] = '4';
			}
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);
	}
	
	function verificarFecha($fecha){
		if(trim($fecha)!=''){
			$pattern = '/^(0?[1-9]|[12][0-9]|3[01])[\/](0?[1-9]|1[012])[\/]\d{4}$/';
			return preg_match($pattern, $fecha);
		}
		return true;
	}
	
	function convertirArray($array){
		$array = json_decode($array);
		$aux = array();
		foreach($array as $row){
			array_push($aux, $row->dato);
		}
		return $aux;
	}
	
	function recibos(){
		include 'get_session_data.php';
                $data['javascript_cache_version'] = $this->javascriptCacheVersion;
		$this->load->view('consulta/recibos_consulta_view', $data);
	}
	
	function getRecibosFiltrados(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['cliente'])&&isset($_POST['desde'])&&isset($_POST['hasta'])&&isset($_POST['tipo'])&&isset($_POST['estado'])){
			$cliente = $_POST['cliente'];
			$desde = $_POST['desde'];
			$hasta = $_POST['hasta'];
			$tipo = $this->convertirArray($_POST['tipo']);
			$estado = $this->convertirArray($_POST['estado']);
			if($this->verificarFecha($desde)&&$this->verificarFecha($hasta)){				
				include 'get_session_data.php';
				if($recibos = $this->contabilidad->getRecibosFiltrados($cliente, $desde, $hasta, $tipo, $estado, $data['Sucursal_Codigo'])){
					unset($retorno['error']);
					$retorno['status'] = 'success';
					$retorno['facturas'] = $recibos;
				}else{
					//No hay recibos
					$retorno['error'] = '3';
				}					
			}else{
				//Fechas con mal formato
				$retorno['error'] = '4';
			}
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);
	}
	
	function getRecibo(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['recibo'])){
			include 'get_session_data.php';
			if($recibo = $this->contabilidad->getReciboParaImpresion($_POST['recibo'], $data['Sucursal_Codigo'])){
				unset($retorno['error']);
				$retorno['status'] = 'success';
				$retorno['recibo'] = $recibo;
				//Para efecto de impresion
				$retorno['sucursal']= $data['Sucursal_Codigo'];
				$retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
				$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");
			}else{
				//No hay recibos
				$retorno['error'] = '3';
			}
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);
	}
	
	function notasCredito(){
		include 'get_session_data.php';
		$this->load->view('consulta/notas_credito_consulta_view', $data);
	}
	
	function getNotasCreditoFiltradas(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		
		if(isset($_POST['cliente'])&&isset($_POST['desde'])&&isset($_POST['hasta'])){
			$cliente = $_POST['cliente'];
			$desde = $_POST['desde'];
			$hasta = $_POST['hasta'];
			
			if($this->verificarFecha($desde)&&$this->verificarFecha($hasta)){				
				include 'get_session_data.php';				
				if($notas = $this->contabilidad->getNotasCreditoFiltrados($cliente, $desde, $hasta, $data['Sucursal_Codigo'])){
					unset($retorno['error']);
					$retorno['status'] = 'success';
					$retorno['notas'] = $notas;		
				}else{
					$retorno['error'] = '3';
				}				
			}else{
				//Fechas con mal formato
				$retorno['error'] = '4';
			}
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);
	}
	
	function getNotaCredito(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['nota'])){
			
			$consecutivo = $_POST['nota'];
			include 'get_session_data.php';
			if($notaCreditoHead = $this->contabilidad->getNotaCreditoHeaderParaImpresion($consecutivo, $data['Sucursal_Codigo'])){
				
				if($notaCreditoBody = $this->contabilidad->getArticulosNotaCredito($consecutivo, $data['Sucursal_Codigo'])){
					
					unset($retorno['error']);
					$retorno['status'] = 'success';
					$retorno['notaHead'] = $notaCreditoHead;
					$retorno['notaBody'] = $this->contabilidad->getArticulosNotaCreditoParaImpresion($consecutivo, $data['Sucursal_Codigo']);
					
					$cliente = $this->cliente->getClientes_Cedula($notaCreditoHead[0]->cliente_cedula);
					$c_array = $this->configuracion->getConfiguracionArray();
                                        
                                        $costo_total = 0;
                                        $iva = 0;
                                        $costo_sin_iva = 0;
                                        $retencion = 0;
                                        $aplicaRetencion = true;
                                        if(!$c_array['aplicar_retencion'] || $cliente[0]->Aplica_Retencion == "1" || $cliente[0]->Cliente_EsExento == "1"){
                                            $aplicaRetencion = false;
                                        }
                                        foreach($notaCreditoBody as $art){
                                            $detalle = $this->contabilidad->getDetalleLineaNotaCredito($art, $aplicaRetencion);
                                            $iva += $detalle["iva"];
                                            $retencion += $detalle["retencion"];
                                            $costo_sin_iva += $detalle["subtotal"];
                                        }
                                        $costo_total += round($iva, intval($c_array["cantidad_decimales"])) + round($retencion, intval($c_array["cantidad_decimales"])) + $costo_sin_iva;
								
                                        $notaCreditoHead[0]->total = $costo_total;
                                        $notaCreditoHead[0]->subtotal = $costo_sin_iva;
                                        $notaCreditoHead[0]->total_iva = $iva;
                                        $notaCreditoHead[0]->retencion = $retencion;
					
					
					
					
					//Para efecto de impresion
					$retorno['sucursal']= $data['Sucursal_Codigo'];
					$retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
					$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");							
				}else{
					$this->retorno['error'] = '12';
				}
			}else{
				$this->retorno['error'] = '11';
			}
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);
	}
	
	function notasDebito(){
		include 'get_session_data.php';
		$this->load->view('consulta/notas_debito_consulta_view', $data);
	}
	
	function getNotasDebitoFiltradas(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		
		if(isset($_POST['desde'])&&isset($_POST['hasta'])){
			$desde = $_POST['desde'];
			$hasta = $_POST['hasta'];
			
			if($this->verificarFecha($desde)&&$this->verificarFecha($hasta)){				
				include 'get_session_data.php';				
				if($notas = $this->contabilidad->getNotasDebitoFiltrados($desde, $hasta, $data['Sucursal_Codigo'])){
					unset($retorno['error']);
					$retorno['status'] = 'success';
					$retorno['notas'] = $notas;		
				}else{
					$retorno['error'] = '3';
				}				
			}else{
				//Fechas con mal formato
				$retorno['error'] = '4';
			}
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);
	}
	
	function getNotaDebito(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['nota'])){
			$consecutivo = $_POST['nota'];
			include 'get_session_data.php';
			if($notaCreditoHead = $this->contabilidad->getHeadNotaDebito($consecutivo, $data['Sucursal_Codigo'])){
				if($notaCreditoBody = $this->contabilidad->getProductosNotaDebito($consecutivo, $data['Sucursal_Codigo'])){
					unset($retorno['error']);
					$retorno['status'] = 'success';
					
					$notaCreditoHead[0] -> entrega = $notaCreditoHead[0] -> entrega ." - ".$this->empresa->getNombreEmpresa($notaCreditoHead[0] -> entrega);
					$notaCreditoHead[0] -> recibe = $notaCreditoHead[0] -> recibe ." - ".$this->empresa->getNombreEmpresa($notaCreditoHead[0] -> recibe);
					
					
					
					$retorno['notaHead'] = $notaCreditoHead;
					$retorno['notaBody'] = $notaCreditoBody;
					
					$total = 0;
					$subtotal = 0;
					$total_iva = 0;
					
					foreach($notaCreditoBody as $art){
						$total = $total + ($art->precio * ($art->cantidad));
						$total_iva = $total_iva + (($art->precio * ($art->cantidad)) * ($notaCreditoHead[0]->iva/100));
						$subtotal = $subtotal + (($art->precio * ($art->cantidad)) - (($art->precio * ($art->cantidad)) * ($notaCreditoHead[0]->iva/100)));
					}
					
					$notaCreditoHead[0]->total = $total;
					$notaCreditoHead[0]->subtotal = $subtotal;
					$notaCreditoHead[0]->total_iva = $total_iva;
														
					//Para efecto de impresion
					$retorno['sucursal']= $data['Sucursal_Codigo'];
					$retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
					$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");							
				}else{
					$this->retorno['error'] = '12';
				}
			}else{
				$this->retorno['error'] = '11';
			}
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);
	}
	
	function retiroParcial(){
		include 'get_session_data.php';
                $data['javascript_cache_version'] = $this->javascriptCacheVersion;
		$this->load->view('consulta/retiros_parciales_consulta_view', $data);
	}
	
	function getRetirosParcialesFiltrados(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		
		if(isset($_POST['desde'])&&isset($_POST['hasta'])){
			$desde = $_POST['desde'];
			$hasta = $_POST['hasta'];
			
			if($this->verificarFecha($desde)&&$this->verificarFecha($hasta)){				
				include 'get_session_data.php';			
				if($retiros = $this->contabilidad->getRetirosParcialesFiltrados($desde, $hasta, $data['Sucursal_Codigo'])){
					unset($retorno['error']);
					$retorno['status'] = 'success';
					$retorno['retiros'] = $retiros;
				}else{
					$retorno['error'] = '3';
				}				
			}else{
				//Fechas con mal formato
				$retorno['error'] = '4';
			}
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);		
	}
	
	
	function getRetiroParcial(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['retiro'])){
			$consecutivo = $_POST['retiro'];
			include 'get_session_data.php';
			if($retiro = $this->contabilidad->getRetiroParcialHeadImpresion($consecutivo)){
				if($billetes = $this->contabilidad->getDenominacionesRetiroParcialPorTipoYMoneda($consecutivo, 'billete', 'colones')){
					if($monedas = $this->contabilidad->getDenominacionesRetiroParcialPorTipoYMoneda($consecutivo, 'moneda', 'colones')){
						if($dolares = $this->contabilidad->getDenominacionesRetiroParcialPorTipoYMoneda($consecutivo, 'billete', 'dolares')){
							unset($retorno['error']);
							$retorno['status'] = 'success';
							$retorno['retiro'] = $retiro;
							$retorno['billetes'] = $billetes;
							$retorno['monedas'] = $monedas;
							$retorno['dolares'] = $dolares;
							
							//Para efecto de impresion
							$retorno['sucursal']= $data['Sucursal_Codigo'];
							$retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
							$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");	
						}else{
							$retorno['error'] = '18';
						}
					}else{
						$retorno['error'] = '17';
					}
				}else{
					$retorno['error'] = '16';
				}
			}else{
				$retorno['error'] = '15';
			}
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);
	}
	
	function proformas(){
		include 'get_session_data.php';
		$data['javascript_cache_version'] = $this->javascriptCacheVersion;
		$this->load->view('consulta/proformas_consulta_view', $data);
	}
	
	function getProformasFiltradas(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['cliente'])&&isset($_POST['desde'])&&isset($_POST['hasta'])){
			$cliente = $_POST['cliente'];
			$desde = $_POST['desde'];
			$hasta = $_POST['hasta'];
			$estado = $this->convertirArray($_POST['estado']);
			if($this->verificarFecha($desde)&&$this->verificarFecha($hasta)){				
				include 'get_session_data.php';
				if($facturas = $this->proforma_m->getProformasFiltradas($cliente, $desde, $hasta, $data['Sucursal_Codigo'], $estado)){
					unset($retorno['error']);
					$retorno['status'] = 'success';
					$retorno['facturas'] = $facturas;
				}else{
					//No hay facturas
					$retorno['error'] = '3';
				}				
			}else{
				//Fechas con mal formato
				$retorno['error'] = '4';
			}
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);
	}
	
	function cierreCaja(){
			include 'get_session_data.php';
			$data['javascript_cache_version'] = $this->javascriptCacheVersion;
			$this->load->view('consulta/cierre_caja_consulta_view', $data);
	}
	
	function getCierresFiltrados(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		
		if(isset($_POST['desde'])&&isset($_POST['hasta'])){
			$desde = $_POST['desde'];
			$hasta = $_POST['hasta'];
			
			if($this->verificarFecha($desde)&&$this->verificarFecha($hasta)){				
				include 'get_session_data.php';				
				if($retiros = $this->contabilidad->getCierresFiltrados($desde, $hasta, $data['Sucursal_Codigo'])){
					unset($retorno['error']);
					$retorno['status'] = 'success';
					$retorno['retiros'] = $retiros;
				}else{
					$retorno['error'] = '3';
				}				
			}else{
				//Fechas con mal formato
				$retorno['error'] = '4';
			}
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);	
	}
	
	function getCierreCaja(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['cierre'])){
			$consecutivo = $_POST['cierre'];
			include 'get_session_data.php';
			$sucursal = $data['Sucursal_Codigo'];
			if($cierre = $this->contabilidad->getCierreCaja($consecutivo, $sucursal)){
					$fechaCierre = $cierre->fechaCruda;
					//Obtener la fecha del cierre que esta antes de este cierre
					$fechaCierreAnterior = $this->contabilidad->getFechaUltimoCierreCajaAntesDeCierreCaja($sucursal, $consecutivo);
					
					$facturas = $this->getPrimeraUltimaFactura($sucursal, $fechaCierre, $fechaCierreAnterior);
		
					$datos['primeraFactura'] = $facturas['primera'];
					$datos['ultimaFactura'] = $facturas['ultima'];
					$datos['bnservicios'] = $cierre->bnservicios;
					
					$retirosParciales = $this->getRetirosParcialesYTotal($sucursal, $fechaCierre, $fechaCierreAnterior);
					
					$datos['retirosParciales'] = $retirosParciales['retiros'];
					$datos['totalRecibosParciales'] = $retirosParciales['total'];
						
					$datos['pagoDatafonos'] = $this->getPagosDatafonos($sucursal, $fechaCierre, $fechaCierreAnterior);
					
					$datos['pagoMixto'] = $this->obtenerPagosMixtos($sucursal, $fechaCierre, $fechaCierreAnterior);
					
					$datos['recibos'] = $this->obtenerRecibosDeDinero($sucursal, $fechaCierre, $fechaCierreAnterior);
					
					$datos['totalFacturasContado'] = $this->obtenerTotalFacturasContado($sucursal, $fechaCierre, $fechaCierreAnterior);
					$datos['totalFacturasContado'] += $datos['pagoMixto']['efectivo'];
					
					$datos['totalCreditos'] = $this->obtenerTotalCreditos($sucursal, $fechaCierre, $fechaCierreAnterior);
					
					$datos['totalNotasCredito'] = $this->obtenerTotalesNotasCredito($sucursal, $fechaCierre, $fechaCierreAnterior);
					
					$datos['detalleNotasCredito'] = $this->contabilidad->getInfoGeneralNotaCreditoPorRangoFecha($sucursal, date('Y-m-d H:i:s', $fechaCierreAnterior), $fechaCierre);

					
					$datos['totalNotasDebito'] = $this->obtenerTotalesNotasDebito($sucursal, $fechaCierre, $fechaCierreAnterior);
					
					$datos['totalFacturasDeposito'] = $this->obtenerTotalFacturasDeposito($sucursal, $fechaCierre, $fechaCierreAnterior);
					
					$datos['vendedores'] = $this->obtenerVendidoPorCadaVendedor($sucursal, $fechaCierre, $fechaCierreAnterior);
					
					$datos['valoresFinales'] = $this->obtenerValoresFinales($sucursal, $fechaCierre, $fechaCierreAnterior);
					
					if($billetes = $this->contabilidad->getDenominacionesCierreCajaPorTipoYMoneda($consecutivo, 'billete', 'colones')){
						if($monedas = $this->contabilidad->getDenominacionesCierreCajaPorTipoYMoneda($consecutivo, 'moneda', 'colones')){
							if($dolares = $this->contabilidad->getDenominacionesCierreCajaPorTipoYMoneda($consecutivo, 'billete', 'dolares')){
									unset($retorno['error']);
									$retorno['status'] = 'success';
									$retorno['cierre'] = $cierre;
									$retorno['datos'] = $datos;
									$retorno['billetes'] = $billetes;
									$retorno['monedas'] = $monedas;
									$retorno['dolares'] = $dolares;
									
									//Para efecto de impresion
									$retorno['sucursal']= $data['Sucursal_Codigo'];
									$retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
									$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");	
							}else{
								//NO CARGO DOLARES
								$retorno['error'] = '13';
							}
						}else{
							//NO CARGO MONEDAS
							$retorno['error'] = '12';
						}
					}else{
						//NO CARGO BILLETES
						$retorno['error'] = '11';
					}
			}else{
				//NO CARGO CIERRE
				$retorno['error'] = '10';
			}
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);
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
		$total = 0;                                                              //         INICIO               FINAL
		if($facturas = $this->contabilidad->getFacturasContadoPorRangoFecha($sucursal,  date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
			foreach($facturas as $factura){
				$total += $factura->Factura_Monto_Total;
			}
		}
		return $total;
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
		
								
		$costo_total = 0;
		$iva = 0;
		$costo_sin_iva = 0;
		$retencion = 0;
		if($notas = $this->contabilidad->getNotaCreditoPorRangoFecha($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
			foreach($notas as $nota){
				
				if($notaCreditoBody = $this->contabilidad->getArticulosNotaCreditoParaImpresion($nota->Consecutivo, $sucursal)){
					
					foreach($notaCreditoBody as $art){
						$cliente = $this->cliente->getClientes_Cedula($nota->Cliente);
						
						$cantidadArt = $art->bueno + $art->defectuoso;
						//Calculamos el precio total de los articulos
						//$precio_total_articulo = (($art->precio)-(($art->precio)*(($art->descuento)/100)))*$cantidadArt;
						$precio_total_articulo = $art->precio*$cantidadArt;
						$precio_total_articulo_sin_descuento = $art->precio*$cantidadArt;
						$precio_articulo_final = $art->precio_final;
						$precio_articulo_final = $precio_articulo_final * $cantidadArt;
						
						//Calculamos los impuestos
						
						$isExento = $art->exento;
						if($isExento=='0'){
							$costo_sin_iva += $precio_total_articulo/(1+(floatval($nota->Por_IVA)/100));
							
							
							$iva_precio_total_cliente = $precio_total_articulo - ($precio_total_articulo/(1+(floatval($nota->Por_IVA)/100)));
							$iva_precio_total_cliente_sin_descuento = $precio_total_articulo_sin_descuento - ($precio_total_articulo_sin_descuento/(1+(floatval($nota->Por_IVA)/100))); 
							
							$precio_final_sin_iva = $precio_articulo_final/(1+(floatval($nota->Por_IVA)/100));
							$iva_precio_final = $precio_articulo_final - $precio_final_sin_iva;
							
							if(!$art->no_retencion){
									$retencion += ($iva_precio_final - $iva_precio_total_cliente_sin_descuento);
							}
						}
						else if($isExento=='1'){
							$costo_sin_iva += $precio_total_articulo;
							//$retencion = 0;
						}
						$costo_total += $precio_total_articulo;

					
					
					}
					
					
					if($cliente[0]->Aplica_Retencion == "1")
						$retencion = 0;
					
					
					
					$iva = $costo_total-$costo_sin_iva;
					$costo_total += $retencion;
				}
			}
		}
		
								
		return array('total'=>$costo_total, 'subtotal'=>$costo_sin_iva, 'iva'=>$iva, 'retencion'=>$retencion);				
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
	
	function obtenerTotalFacturasDeposito($sucursal, $fechaHoraActual, $fechaUltimoCierra){
		$total = 0;
		if($facturas = $this->contabilidad->getFacturasDepositoPorRangoFecha($sucursal, date('Y-m-d H:i:s', $fechaUltimoCierra), $fechaHoraActual)){
			
			foreach($facturas as $factura){
				$total += $factura->Factura_Monto_Total;
			}
		}
		return $total;
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
	
	function cambiosCodigo(){
		include 'get_session_data.php';
		$empresas_actuales = $this->empresa->get_empresas_ids_array();
		$data['Familia_Empresas'] = $empresas_actuales;
		$this->load->view('consulta/cambios_codigo_consulta_view', $data);
	}
	
	function getCambiosCodigoFiltrados(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		
		if(isset($_POST['desde'])&&isset($_POST['hasta'])&&isset($_POST['sucursal'])){
			$desde = $_POST['desde'];
			$hasta = $_POST['hasta'];
			$sucursal = $_POST['sucursal'];
			
			if($this->verificarFecha($desde)&&$this->verificarFecha($hasta)){		
				if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){		
					include 'get_session_data.php';				
					if($cambios = $this->articulo->getCambiosCodigoRangoFechas($sucursal, $desde, $hasta)){
						unset($retorno['error']);
						$retorno['status'] = 'success';
						$retorno['cambios'] = $cambios;		
					}else{
						$retorno['error'] = '3';
					}	
				}else{
					$retorno['error'] = '5';
				}			
			}else{
				//Fechas con mal formato
				$retorno['error'] = '4';
			}
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);
	}
	
	function getCambioCodigo(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['cambio']) && isset($_POST['sucursal'])){
			$consecutivo = $_POST['cambio'];
			$sucursal = $_POST['sucursal'];
			if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){		
				include 'get_session_data.php';				
				if($head = $this->articulo->getCambioCodigoHeader($consecutivo, $sucursal)){
					if($body = $this->articulo->getCambioCodigoArticulos($consecutivo)){
						$retorno['cambioHead'] = $head;
						$retorno['cambioBody'] = $body;
						unset($retorno['error']);
						$retorno['status'] = 'success';
						//Para efecto de impresion
						$retorno['sucursal']= $sucursal;
						$retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
						$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");		
					}else{
						$retorno['error'] = '5';
					}
				}else{
					$retorno['error'] = '4';
				}
			}else{
				$retorno['error'] = '3';
			}	
		}else{
			//URL MALA
			$retorno['error'] = '2';
		}
		echo json_encode($retorno);
	}

        function consignaciones(){
		include 'get_session_data.php';
		$data['javascript_cache_version'] = $this->javascriptCacheVersion;
                $conf_array = $this->configuracion->getConfiguracionArray();
                $data['Familia_Empresas'] = $this->empresa->get_empresas_ids_array();
                $data['porcentaje_iva'] = $conf_array['iva'];
                $data['cantidad_decimales'] = $conf_array['cantidad_decimales'];
                $data['aplicar_retencion'] = $conf_array['aplicar_retencion'];
		$this->load->view('consulta/consignaciones_consulta_view', $data);
	}
        
        function getConsignacionesFiltrados(){
            $retorno["error"] = "No se pudo procesar su solicitud";
            $retorno["status"] = "error";
            
            $consigna = trim($_POST["consigna"]) == "-1" ? "" : trim($_POST["consigna"]);
            $recibe = trim($_POST["recibe"]) == "-1" ? "" : trim($_POST["recibe"]);
            $desde = trim($_POST["desde"]);
            $hasta = trim($_POST["hasta"]);
            $tipo = trim($_POST["tipo"]);
            
            if($consignaciones = $this->contabilidad->getConsignacionesFiltradas($consigna, $recibe, $desde, $hasta, $tipo)){
                unset($retorno["error"]);
                $retorno["status"] = "success";
                $retorno["consignaciones"] = $consignaciones;
            }else{
                $retorno["error"] = "No hay consignaciones con los filtros seleccionados";
                $retorno["status"] = "error";
            }
            
            echo json_encode($retorno);
        }
        
        
        
        function comprobantesElectronicos(){
            include 'get_session_data.php';
            $data['javascript_cache_version'] = $this->javascriptCacheVersion;
            $this->load->view('consulta/comprobantes_consulta_view', $data);
	}
        
        function obtenerComprobantesTabla(){
            include PATH_USER_DATA;
            //Un array que contiene el nombre de las columnas que se pueden ordenar
            $columnas = array(
                            '0' => 'Clave',
                            '1' => 'ConsecutivoHacienda',
                            '2' => 'ReceptorIdentificacion',
                            '3' => 'FechaEmision',
                            '4' => 'RespuestaHaciendaEstado'
                        );
            
            $query = $this->contabilidad->obtenerComprobantesParaTabla($columnas[$_POST['order'][0]['column']], 
                    $_POST['order'][0]['dir'], 
                    $_POST['search']['value'], 
                    intval($_POST['start']), 
                    intval($_POST['length']), 
                    $data['Sucursal_Codigo'],
                    $_POST['tipodocumento']);

            $ruta_imagen = base_url('application/images/Icons');
            $comprobantesAMostrar = array();
            foreach($query->result() as $art){
                $htmlPdf = "";
                $htmlXML = "";
                $htmlXMLRespuesta = "";
                $htmlReenvioCorreo = "";
                $rutaWeb = $this->contabilidad->getFinalPathWeb(strtolower($_POST['tipodocumento']), $art->fecha);
                switch($_POST['tipodocumento']){
                    case "FE":
                        $htmlPdf = "<a target='_blank' href='".$rutaWeb.$art->clave.".pdf' ><img src=".$ruta_imagen."/icon-pdf.png width='21' height='21' title='Ver PDF'></a>";
                        $htmlXML = "<a target='_blank' href='".$rutaWeb.$art->clave.".xml' ><img src=".$ruta_imagen."/icon-xml.png width='21' height='21' title='Ver XML'></a>";
                        $htmlXMLRespuesta = "<a target='_blank' href='".$rutaWeb.$art->clave."-respuesta.xml' ><img src='".$ruta_imagen."/Information_icon.png' width='21' height='21' title='Ver Respuesta de Hacienda'></a>";
                    break;
                    case "NC":
                        $htmlPdf = "<a target='_blank' href='".$rutaWeb.$art->clave.".pdf' ><img src=".$ruta_imagen."/icon-pdf.png width='21' height='21' title='Ver PDF'></a>";
                        $htmlXML = "<a target='_blank' href='".$rutaWeb.$art->clave.".xml' ><img src=".$ruta_imagen."/icon-xml.png width='21' height='21' title='Ver XML'></a>";
                        $htmlXMLRespuesta = "<a target='_blank' href='".base_url('')."consulta/verXMLHacienda?clave=".$art->clave."&tipo=".$_POST['tipodocumento']."' ><img src='".$ruta_imagen."/Information_icon.png' width='21' height='21' title='Ver Respuesta de Hacienda'></a>";
                    break;
                    case "MR":
                        $htmlXML = "<a target='_blank' href='".$rutaWeb.$art->clave."-".$art->consecutivo.".xml' ><img src=".$ruta_imagen."/icon-xml.png width='21' height='21' title='Ver XML'></a>";
                        $htmlXMLRespuesta = "<a target='_blank' href='".base_url('')."consulta/verXMLHacienda?clave=".$art->clave."&tipo=".$_POST['tipodocumento']."' ><img src='".$ruta_imagen."/Information_icon.png' width='21' height='21' title='Ver Respuesta de Hacienda'></a>";
					break;
					case "FEC":
                        $htmlPdf = "<a target='_blank' href='".$rutaWeb.$art->clave.".pdf' ><img src=".$ruta_imagen."/icon-pdf.png width='21' height='21' title='Ver PDF'></a>";
                        $htmlXML = "<a target='_blank' href='".$rutaWeb.$art->clave.".xml' ><img src=".$ruta_imagen."/icon-xml.png width='21' height='21' title='Ver XML'></a>";
                        $htmlXMLRespuesta = "<a target='_blank' href='".$rutaWeb.$art->clave."-respuesta.xml' ><img src='".$ruta_imagen."/Information_icon.png' width='21' height='21' title='Ver Respuesta de Hacienda'></a>";
                    break;
                }
                
                if($art->estado != "aceptado" && $art->estado != "rechazado" && $art->estado != "recibido" && $art->estado != "procesando"){
                    $htmlXMLRespuesta = "";
                }
                
                if($_POST['tipodocumento'] != "MR"){
                    if(($art->estado == "aceptado" || $art->estado == "rechazado") && filter_var($art->email, FILTER_VALIDATE_EMAIL)){
                        $htmlReenvioCorreo = "<a href='#' onclick='reenviarCorreo(\"".$art->clave."\", \"".$_POST['tipodocumento']."\")'><img src=".$ruta_imagen."/mail.png width='21' height='21' title='Reenviar Correo a Cliente'></a>";
                    }
                }
                $auxArray = array(
                            $art->clave,
                            $art->consecutivo,
                            $art->cliente_identificacion." - ".$art->cliente_nombre,
                            date("h:i:s a d-m-Y", strtotime($art->fecha)),
                            $_POST['tipodocumento'] == "MR" ? "" : $this->getCorreoEnviadoComprobanteHTML($art->correo_enviado),
                            $this->getEstadoComprobanteHTML($art->estado),
                            "<div class='tab_opciones'>
                                    $htmlPdf
                                    $htmlXML
                                    $htmlXMLRespuesta
                                    $htmlReenvioCorreo
                                    ".(($art->estado == "aceptado" || $art->estado == "rechazado" || $art->estado == "recibido" || $art->estado == "procesando") ? "" : "<a href='#' onclick='reenviarXML(\"$art->clave\")'><img src=".$ruta_imagen."/upload.png width='21' height='21' title='Reenviar Documento a Hacienda'></a>")."
                            </div>"
                    );
                    array_push($comprobantesAMostrar, $auxArray);
            }

            $filtrados = $this->contabilidad->obtenerComprobantesParaTablaFiltrados($columnas[$_POST['order'][0]['column']], $_POST['order'][0]['dir'], $_POST['search']['value'], intval($_POST['start']), intval($_POST['length']), $data['Sucursal_Codigo'],
                    $_POST['tipodocumento']);

            $retorno = array(
                            'draw' => $_POST['draw'],
                            'recordsTotal' => $this->contabilidad->getTotalComprobantesEnSucursal($data['Sucursal_Codigo'], $_POST['tipodocumento']),
                            'recordsFiltered' => $filtrados -> num_rows(),
                            'data' => $comprobantesAMostrar
                        );
            echo json_encode($retorno);
        }
        
        private function getCorreoEnviadoComprobanteHTML($estado){
            if($estado == 1){
                return "<div class='estado-label success'>Enviado</div>";
            }
            
            return "<div class='estado-label error'>No enviado</div>";
        }
        
        private function getEstadoComprobanteHTML($estado){
            if($estado == "aceptado"){
                return "<div class='estado-label success'>Aceptado</div>";
            }
            if($estado == "rechazado"){
                return "<div class='estado-label error'>Rechazado</div>";
            }
            if($estado == "sin_enviar"){
                return "<div class='estado-label info'>Sin Enviar</div>";
            }
            
            return "<div class='estado-label warning'>".ucfirst($estado)."</div>";
        }
        
        public function verXMLHacienda(){
            $clave = @$_GET["clave"];
            $tipo = @$_GET["tipo"];
            if($clave != ""){
                if($tipo == "FE"){
                    if($factura = $this->factura->getFacturaElectronicaByClave($clave)){
                        $before = array('<','>');
                        $after = array('&lt;','&gt;');
                        $xml = str_replace($before,$after,base64_decode($factura->RespuestaHaciendaXML));
                        echo "<pre>".$xml;
                    }else{
                        die("No existe factura electronica");
                    }
                }else if($tipo == "NC"){
                    if($nota = $this->contabilidad->getNotaCreditoElectronicaByClave($clave)){
                        $before = array('<','>');
                        $after = array('&lt;','&gt;');
                        $xml = str_replace($before,$after,base64_decode($nota->RespuestaHaciendaXML));
                        echo "<pre>".$xml;
                    }else{
                        die("No existe nota credito electronica");
                    }
                }else if($tipo == "MR"){
                    if($nota = $this->contabilidad->getMensajeReceptorElectronicoByClave($clave)){
                        $before = array('<','>');
                        $after = array('&lt;','&gt;');
                        $xml = str_replace($before,$after,base64_decode($nota->RespuestaHaciendaXML));
                        echo "<pre>".$xml;
                    }else{
                        die("No existe mensaje receptor");
                    }
                }else{
                    die("Tipo de documento no valido");
                }
            }else{
                die("La clave no puede ser vacia");
            }
        }
        
        public function reenviarDocumento(){
            $clave = @$_POST["clave"];
            $tipo = @$_POST["tipo"];
            $retorno = array("status"=>0, "error"=>"No se pudo procesar la solicitud");
            if($clave != ""){
                if($tipo != ""){
                    include PATH_USER_DATA;
                    switch($tipo){
                        case "FE":
                            if($factura = $this->factura->getFacturaElectronicaByClave($clave)){
                                if($cliente = $this->cliente->getClientes_Cedula($factura->ReceptorIdentificacion)){
                                    if($empresaData = $this->empresa->getEmpresa($factura->Sucursal)){
                                        $facturaa = (object) array("Factura_Consecutivo" => $factura->Consecutivo, "TB_02_Sucursal_Codigo" => $factura->Sucursal);
                                        $cliente = $cliente[0];
                                        $empresa = $empresaData[0];
                                        $resFacturaElectronica = array("data" => array("situacion"=>"normal"));
                                        $responseCheck = array("factura" => $facturaa, "cliente"=>$cliente, "empresa"=>$empresa);
                                        $result = $this->factura->envioHacienda($resFacturaElectronica, $responseCheck);
                                        if($result["status"]){ // Factura fue recibida y aceptada
                                            $this->factura->guardarPDFFactura($responseCheck["factura"]->Factura_Consecutivo, $data['Sucursal_Codigo']);
                                            if(!$responseCheck["cliente"]->NoReceptor){
                                                require_once PATH_API_CORREO;
                                                $apiCorreo = new Correo();
                                                $attachs = array(
                                                    $this->factura->getFinalPath("fe", $factura->FechaEmision).$factura->Clave.".xml",
                                                    $this->factura->getFinalPath("fe", $factura->FechaEmision).$factura->Clave."-respuesta.xml",
                                                    $this->factura->getFinalPath("fe", $factura->FechaEmision).$factura->Clave.".pdf");
                                                if($apiCorreo->enviarCorreo($responseCheck["cliente"]->Cliente_Correo_Electronico, "Factura Electrónica #".$responseCheck["factura"]->Factura_Consecutivo." | ".$responseCheck["empresa"]->Sucursal_Nombre, "Este mensaje se envió automáticamente a su correo al generar una factura electrónica bajo su nombre.", "Factura Electrónica - ".$responseCheck["empresa"]->Sucursal_Nombre, $attachs)){
                                                    $this->factura->marcarEnvioCorreoFacturaElectronica($data['Sucursal_Codigo'], $responseCheck["factura"]->Factura_Consecutivo);
                                                }
                                            }
                                        }
                                        $retorno["status"] = 1;
                                        unset($retorno["error"]);
                                    }else{
                                        $retorno["error"] = "No existe sucursal para dicha factura";
                                    }
                                }else{
                                    $retorno["error"] = "No existe cliente para dicha factura";
                                }
                            }else{
                                $retorno["error"] = "No existe factura electronica";
                            }
                        break;
                        case "NC":
                            if($nota = $this->contabilidad->getNotaCreditoElectronicaByClave($clave)){
                                $this->contabilidad->enviarNotaCreditoElectronicaAHacienda($nota->Consecutivo, $nota->Sucursal);
                                $retorno["status"] = 1;
                                unset($retorno["error"]);
                            }else{
                                $retorno["error"] = "No existe nota credito electronica";
                            }
                        break;
                        case "MR":
                            if($mensaje = $this->contabilidad->getMensajeReceptorElectronicoByClave($clave)){
                                $this->contabilidad->enviarMensajeReceptorHacienda($mensaje->Consecutivo, $mensaje->Sucursal);
                                $retorno["status"] = 1;
                                unset($retorno["error"]);
                            }else{
                                $retorno["error"] = "No existe mensaje receptor";
                            }
						break;
						case "FEC":
                            if($factura = $this->contabilidad->getFacturaDeCompraElectronicaByClave($clave)){
								$this->factura->enviarFacturaElectronicaDeCompraAHacienda($factura->Consecutivo, $factura->Sucursal);
                                $retorno["status"] = 1;
                                unset($retorno["error"]);
                            }else{
                                $retorno["error"] = "No existe mensaje receptor";
                            }
                        break;
                    }
                }else{
                    $retorno["error"] = "El tipo no puede ser vacio";
                }
            }else{
                $retorno["error"] = "La clave no puede ser vacia";
            }
            echo json_encode($retorno);
        }
        
        public function reenviarCorreo(){
            $clave = @$_POST["clave"];
            $tipo = @$_POST["tipo"];
            $retorno = array("status"=>0, "error"=>"No se pudo procesar la solicitud");
            if($clave != ""){
                if($tipo != ""){
                    include PATH_USER_DATA;
                    switch($tipo){
                        case "FE":
                            if($factura = $this->factura->getFacturaElectronicaByClave($clave)){
                                if(filter_var($factura->ReceptorEmail, FILTER_VALIDATE_EMAIL)){
                                    $empresa = $this->empresa->getEmpresa($factura->Sucursal)[0];
                                    require_once PATH_API_CORREO;
                                    $apiCorreo = new Correo();
                                    $attachs = array(
                                        $this->factura->getFinalPath("fe", $factura->FechaEmision).$factura->Clave.".xml",
                                        $this->factura->getFinalPath("fe", $factura->FechaEmision).$factura->Clave."-respuesta.xml",
                                        $this->factura->getFinalPath("fe", $factura->FechaEmision).$factura->Clave.".pdf");
                                    if($apiCorreo->enviarCorreo($factura->ReceptorEmail, "Factura Electrónica #".$factura->Consecutivo." | ".$empresa->Sucursal_Nombre, "Este mensaje se envió automáticamente a su correo al generar una factura electrónica bajo su nombre.", "Factura Electrónica - ".$empresa->Sucursal_Nombre, $attachs)){
                                        $this->factura->marcarEnvioCorreoFacturaElectronica($factura->Sucursal, $factura->Consecutivo);
                                        $retorno["status"] = 1;
                                        unset($retorno["error"]);
                                    }
                                }
                            }else{
                                $retorno["error"] = "No existe factura electronica";
                            }
                        break;
                        case "NC":
                            if($nota = $this->contabilidad->getNotaCreditoElectronicaByClave($clave)){
                                if(filter_var($nota->ReceptorEmail, FILTER_VALIDATE_EMAIL)){
                                    $empresa = $this->empresa->getEmpresa($nota->Sucursal)[0];
                                    require_once PATH_API_CORREO;
                                    $apiCorreo = new Correo();
                                    $attachs = array(
                                        $this->contabilidad->getFinalPath("nc", $nota->FechaEmision).$nota->Clave.".xml",
                                        $this->contabilidad->getFinalPath("nc", $nota->FechaEmision).$nota->Clave.".pdf");
                                    if($apiCorreo->enviarCorreo($nota->ReceptorEmail, "Nota Crédito #{$nota->Consecutivo} | ".$empresa->Sucursal_Nombre, "Este mensaje se envió automáticamente a su correo al generar una nota crédito bajo su nombre.", "Nota Crédito Electrónica - ".$empresa->Sucursal_Nombre, $attachs)){
                                        $this->contabilidad->marcarEnvioCorreoNotaCreditoElectronica($nota->Sucursal, $nota->Consecutivo);
                                        $retorno["status"] = 1;
                                        unset($retorno["error"]);
                                    }
                                }
                            }else{
                                $retorno["error"] = "No existe nota credito electronica";
                            }
                            
                        break;
                    }
                }else{
                    $retorno["error"] = "El tipo no puede ser vacio";
                }
            }else{
                $retorno["error"] = "La clave no puede ser vacia";
            }
            echo json_encode($retorno);
        }
        
} 

?>