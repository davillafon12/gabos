<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');

class impresion extends CI_Controller {

	private $tokenSeguridad = '';
	private $retorno = array();
	private $numPagina = 0;
	private $cantidadPaginas = 1;
	
	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);
		$this->load->model('factura','',TRUE);
		$this->load->model('contabilidad','',TRUE);
		$this->load->model('proforma_m','',TRUE);
		$this->load->model('banco','',TRUE);
		include 'get_session_data.php'; //Esto es para traer la informacion de la sesion
		//Generamos el token de seguridad, el cual debe coincidir con el token de llegada
		$this->tokenSeguridad = md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");
		//Este token es simplemente para evitar que cualquiera pueda imprimir o obtener informacion de la factura
		//El token es generado desde el servidor nada mas
		$this->retorno['status'] = 'error';
		$this->retorno['error'] = '0';
	}

	function index()
	{
		/*
			PARAMETROS DE LA URL
			d = tipo de documento (Factura, proforma, recibo)
			i = tipo impresion (Termica/Matrix, Hoja Normal)
			n = numero de documento
			s = sucursal del documento
			t = token de seguridad
		*/
		if(isset($_GET['t'])){
			if($_GET['t']==$this->tokenSeguridad){
				if(isset($_GET['d'])&&isset($_GET['i'])){
					/*
						t = termica / matrix de puntos
						c = carta A4
					*/
					switch($_GET['i']){
						case 't':
							$this->filtrarDocumentosTermica();
						break;
						case 'c':
							$this->filtrarDocumentosCarta();
							//Salimos del script ya que el pdf es la salida final y no el json
							die();
						break;
						default:
							$this->retorno['error'] = '4';
						break;
					}
				}else{
					$this->retorno['error'] = '3';
				}
			}else{
				$this->retorno['error'] = '2';
			}
		}else{
			$this->retorno['error'] = '1';
		}	
		echo $_GET['callback'].'('.json_encode($this->retorno).')';
	}
	
	private function filtrarDocumentosTermica(){
		/*
			f = factura
			r = recibo
			nc = nota credito
			nb = nota debito
		*/
		
		switch($_GET['d']){
			case 'f':				
				if(isset($_GET['n'])&&isset($_GET['s'])){
					$sucursal = $_GET['s'];
					$consecutivo = $_GET['n'];
					
					if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){
						if($facturaHead = $this->factura->getFacturasHeadersImpresion($consecutivo, $sucursal)){
							//Valoramos si un credito para poner la fecha de vencimiento
							if($facturaHead[0] -> tipo == 'credito'){
								$diasCredito = $this->factura->getCreditoClienteDeFactura($consecutivo, $sucursal, $facturaHead[0] -> cliente_ced);
								$facturaHead[0] -> diasCredito = $diasCredito;
								$date = strtotime("+$diasCredito days", strtotime($facturaHead[0] -> fecha) );
								$facturaHead[0] -> fechaVencimiento = date('d-m-Y',$date);
							}elseif($facturaHead[0] -> tipo == 'mixto'){								
								$cantidadPagaTarjeta = $this->factura->getMontoPagoTarjetaMixto($sucursal, $consecutivo);
								$cantidadPagaContado = $facturaHead[0]->total - $cantidadPagaTarjeta;
								//Valorar si fue en colones o dolares								
								if($facturaHead[0] -> moneda == 'dolares'){
									$cantidadPagaTarjeta = $cantidadPagaTarjeta/$facturaHead[0] -> cambio;
									$cantidadPagaContado = $cantidadPagaContado/$facturaHead[0] -> cambio;
								}	
								$facturaHead[0] -> cantidadTarjeta = $cantidadPagaTarjeta;
								$facturaHead[0] -> cantidadContado = $cantidadPagaContado;
							}elseif($facturaHead[0] -> tipo == 'apartado'){								
								$abono = $this->factura->getAbonoApartado($sucursal, $consecutivo);
								//Valorar si fue en colones o dolares								
								if($facturaHead[0] -> moneda == 'dolares'){
									$abono = $abono/$facturaHead[0] -> cambio;
								}
								$facturaHead[0] -> abono = $abono;
							}
							
							//Costos totales
							$subtotal = $facturaHead[0]->subtotal;
							$totalIVA = $facturaHead[0]->total_iva;
							$total = $facturaHead[0]->total;
							//Valoramos si es en dolares
							if($facturaHead[0]->moneda=='dolares'){
								$facturaHead[0]->subtotal = $subtotal/$facturaHead[0]->cambio;
								$facturaHead[0]->total_iva = $totalIVA/$facturaHead[0]->cambio;
								$facturaHead[0]->total = $total/$facturaHead[0]->cambio;
							}		
							if($facturaBody = $this->factura->getArticulosFacturaImpresion($consecutivo, $sucursal)){
							
								//Valoramos si es en dolares
								if($facturaHead[0]->moneda=='dolares'){
									for($i = 0; $i<sizeOf($facturaBody); $i++){
										$facturaBody[$i]->precio = ($facturaBody[$i]->precio)/$facturaHead[0]->cambio;
									}
								}
								
								$this->retorno['status'] = 'success';
								unset($this->retorno['error']);
								
								$this->retorno['empresa'] = $empresa;
								$this->retorno['fHead'] = $facturaHead;
								$this->retorno['fBody'] = $facturaBody;
							}else{
								$this->retorno['error'] = '9';
							}
						}else{
							$this->retorno['error'] = '8';
						}						
					}else{
						$this->retorno['error'] = '7';
					}
				}else{
					$this->retorno['error'] = '6';
				}
			break;
			case 'r':
				if(isset($_GET['n'])&&isset($_GET['s'])){
					$sucursal = $_GET['s'];
					$consecutivo = $_GET['n'];
					if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){
						$recibos = explode(",",$consecutivo);
						if(sizeOf($recibos>=1)){
							$recibosDevolver = array();
							foreach($recibos as $recibo){
								if($recibo = $this->contabilidad->getReciboParaImpresion($recibo, $sucursal)){
									
									//$recibo[0]->saldo_anterior = 0;
									//$saldoAnterior = $this->contabilidad->getSaldoAnteriorRecibo($recibo[0]->recibo, $recibo[0]->c);
									if($saldoAnterior = $this->contabilidad->getSaldoAnteriorRecibo($recibo[0]->recibo, $recibo[0]->c)){
										$recibo[0]->saldo_anterior = $saldoAnterior;
									}else{
										$recibo[0]->saldo_anterior = $recibo[0]->Saldo_inicial;
									}
									
									//Si es un recibo de un apartado hay que valorar el abono
									$facturaHead = $this->factura->getFacturasHeadersImpresion($recibo[0]->factura, $sucursal);
									if($facturaHead[0]->tipo=='apartado'){
										$abono = $this->factura->getAbonoApartado($sucursal, $recibo[0]->factura);
										$saldoAnterior = $recibo[0]->saldo_anterior;
										$recibo[0]->saldo_anterior = $saldoAnterior - $abono ;
									}
									
									array_push($recibosDevolver,$recibo);
								}else{
									//Error al carga de la bd algun recibo
									$this->retorno['error'] = '11';
									break;
								}
							}
							if($this->retorno['error']!='11'){ //Si no se cargo algun recibo salirse
								unset($this->retorno['error']);
								$this->retorno['status'] = 'success';
								$this->retorno['recibos'] = $recibosDevolver;
								$this->retorno['empresa'] = $empresa;
							}
						}else{
							//No vienen recibos
							$this->retorno['error'] = '10';
						}
					}else{
						$this->retorno['error'] = '7';
					}
				}else{
					$this->retorno['error'] = '6';
				}
			break;
			case 'nc':
				if(isset($_GET['n'])&&isset($_GET['s'])){
					$sucursal = $_GET['s'];
					$consecutivo = $_GET['n'];
					if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){
						if($notaCreditoHead = $this->contabilidad->getNotaCreditoHeaderParaImpresion($consecutivo, $sucursal)){
							if($notaCreditoBody = $this->contabilidad->getArticulosNotaCreditoParaImpresion($consecutivo, $sucursal)){
								unset($this->retorno['error']);
								$this->retorno['status'] = 'success';
								$this->retorno['empresa'] = $empresa;
								$this->retorno['notaHead'] = $notaCreditoHead;
								$this->retorno['notaBody'] = $notaCreditoBody;
								
								$total = 0;
								$subtotal = 0;
								$total_iva = 0;
								
								foreach($notaCreditoBody as $art){
									$total = $total + ($art->precio * ($art->bueno + $art->defectuoso));
									$total_iva = $total_iva + (($art->precio * ($art->bueno + $art->defectuoso)) * ($notaCreditoHead[0]->iva/100));
									$subtotal = $subtotal + (($art->precio * ($art->bueno + $art->defectuoso)) - (($art->precio * ($art->bueno + $art->defectuoso)) * ($notaCreditoHead[0]->iva/100)));
								}
								
								$notaCreditoHead[0]->total = $total;
								$notaCreditoHead[0]->subtotal = $subtotal;
								$notaCreditoHead[0]->total_iva = $total_iva;							
							}else{
								$this->retorno['error'] = '12';
							}
						}else{
							$this->retorno['error'] = '11';
						}					
					}else{
						$this->retorno['error'] = '7';
					}
				}else{
					$this->retorno['error'] = '6';
				}
			break;
			case 'nd':
				if(isset($_GET['n'])&&isset($_GET['s'])){
					$sucursal = $_GET['s'];
					$consecutivo = $_GET['n'];
					if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){
						if($notaDebitoHead = $this->contabilidad->getHeadNotaDebito($consecutivo, $sucursal)){
							if($notaDebitoBody = $this->contabilidad->getProductosNotaDebito($consecutivo, $sucursal)){
								unset($this->retorno['error']);
								$this->retorno['status'] = 'success';
								$this->retorno['empresa'] = $empresa;
								$this->retorno['notaHead'] = $notaDebitoHead;
								$this->retorno['notaBody'] = $notaDebitoBody;
								
								$total = 0;
								$subtotal = 0;
								$total_iva = 0;
								
								foreach($notaDebitoBody as $art){
									$total = $total + ($art->precio * $art->cantidad);
									$total_iva = $total_iva + (($art->precio * $art->cantidad) * ($notaDebitoHead[0]->iva/100));
									$subtotal = $subtotal + (($art->precio * $art->cantidad) - (($art->precio * $art->cantidad) * ($notaDebitoHead[0]->iva/100)));
								}
								
								$notaDebitoHead[0]->total = $total;
								$notaDebitoHead[0]->subtotal = $subtotal;
								$notaDebitoHead[0]->total_iva = $total_iva;
							}else{
								$this->retorno['error'] = '14';
							}
						}else{
							$this->retorno['error'] = '13';
						}					
					}else{
						$this->retorno['error'] = '7';
					}
				}else{
					$this->retorno['error'] = '6';
				}
			break;
			case 'rp':
				if(isset($_GET['n'])&&isset($_GET['s'])){
					$sucursal = $_GET['s'];
					$consecutivo = $_GET['n'];
					if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){
						if($retiro = $this->contabilidad->getRetiroParcialHeadImpresion($consecutivo)){
							if($billetes = $this->contabilidad->getDenominacionesRetiroParcialPorTipoYMoneda($consecutivo, 'billete', 'colones')){
								if($monedas = $this->contabilidad->getDenominacionesRetiroParcialPorTipoYMoneda($consecutivo, 'moneda', 'colones')){
									if($dolares = $this->contabilidad->getDenominacionesRetiroParcialPorTipoYMoneda($consecutivo, 'billete', 'dolares')){
										unset($this->retorno['error']);
										$this->retorno['status'] = 'success';
										$this->retorno['empresa'] = $empresa;
										$this->retorno['retiro'] = $retiro;
										$this->retorno['billetes'] = $billetes;
										$this->retorno['monedas'] = $monedas;
										$this->retorno['dolares'] = $dolares;
									}else{
										$this->retorno['error'] = '18';
									}
								}else{
									$this->retorno['error'] = '17';
								}
							}else{
								$this->retorno['error'] = '16';
							}
						}else{
							$this->retorno['error'] = '15';
						}
					}else{
						$this->retorno['error'] = '7';
					}
				}else{
					$this->retorno['error'] = '6';
				}
			break;
			default:
				$this->retorno['error'] = '5';
			break;
		}	
	}
	
	
	
	private function filtrarDocumentosCarta(){
		/*
			f = factura
			r = recibo
			nc = nota credito
			nb = nota debito
			t = traspaso
			cc = cierre caja
		*/
		switch($_GET['d']){
			case 'f':				
				if(isset($_GET['n'])&&isset($_GET['s'])){
					$sucursal = $_GET['s'];
					$consecutivo = $_GET['n'];
					if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){
						if($facturaHead = $this->factura->getFacturasHeadersImpresion($consecutivo, $sucursal)){
							//Valoramos si un credito para poner la fecha de vencimiento
							if($facturaHead[0] -> tipo == 'credito'){
								$diasCredito = $this->factura->getCreditoClienteDeFactura($consecutivo, $sucursal, $facturaHead[0] -> cliente_ced);
								$facturaHead[0] -> diasCredito = $diasCredito;
								$date = strtotime("+$diasCredito days", strtotime($facturaHead[0] -> fecha) );
								$facturaHead[0] -> fechaVencimiento = date('d-m-Y',$date);
							}elseif($facturaHead[0] -> tipo == 'mixto'){
								$cantidadPagaTarjeta = $this->factura->getMontoPagoTarjetaMixto($sucursal, $consecutivo);
								$cantidadPagaContado = $facturaHead[0]->total - $cantidadPagaTarjeta;
								
								//Valorar si fue en colones o dolares								
								if($facturaHead[0] -> moneda == 'dolares'){
									$cantidadPagaTarjeta = $cantidadPagaTarjeta/$facturaHead[0] -> cambio;
									$cantidadPagaContado = $cantidadPagaContado/$facturaHead[0] -> cambio;
								}						
								
								$facturaHead[0] -> cantidadTarjeta = $cantidadPagaTarjeta;
								$facturaHead[0] -> cantidadContado = $cantidadPagaContado;
							}elseif($facturaHead[0] -> tipo == 'apartado'){								
								$abono = $this->factura->getAbonoApartado($sucursal, $consecutivo);
								//Valorar si fue en colones o dolares								
								if($facturaHead[0] -> moneda == 'dolares'){
									$abono = $abono/$facturaHead[0] -> cambio;
								}
								$facturaHead[0] -> abono = $abono;
							}
							
							if($facturaBody = $this->factura->getArticulosFacturaImpresion($consecutivo, $sucursal)){
								
								$this->facturaPDF($empresa, $facturaHead, $facturaBody);								
							}else{
								$this->retorno['error'] = '9';
							}
						}else{
							$this->retorno['error'] = '8';
						}						
					}else{
						$this->retorno['error'] = '7';
					}
				}else{
					$this->retorno['error'] = '6';
				}
			break;
			case 'r':
				if(isset($_GET['n'])&&isset($_GET['s'])){
					$sucursal = $_GET['s'];
					$consecutivo = $_GET['n'];
					if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){
						$recibos = explode(",",$consecutivo);
						if(sizeOf($recibos>=1)){
							$recibosDevolver = array();
							foreach($recibos as $recibo){
								if($recibo = $this->contabilidad->getReciboParaImpresion($recibo, $sucursal)){
									
									//$recibo[0]->saldo_anterior = 0;
									//$saldoAnterior = $this->contabilidad->getSaldoAnteriorRecibo($recibo[0]->recibo, $recibo[0]->c);
									if($saldoAnterior = $this->contabilidad->getSaldoAnteriorRecibo($recibo[0]->recibo, $recibo[0]->c)){
										$recibo[0]->saldo_anterior = $saldoAnterior;
									}else{
										$recibo[0]->saldo_anterior = $recibo[0]->Saldo_inicial;
									}
									
									//Si es un recibo de un apartado hay que valorar el abono
									$facturaHead = $this->factura->getFacturasHeadersImpresion($recibo[0]->factura, $sucursal);
									if($facturaHead[0]->tipo=='apartado'){
										$abono = $this->factura->getAbonoApartado($sucursal, $recibo[0]->factura);
										$saldoAnterior = $recibo[0]->saldo_anterior;
										$recibo[0]->saldo_anterior = $saldoAnterior - $abono ;
									}
									
									array_push($recibosDevolver,$recibo);
								}else{
									//Error al carga de la bd algun recibo
									$this->retorno['error'] = '11';
									break;
								}
							}
							if($this->retorno['error']!='11'){ //Si no se cargo algun recibo salirse
								$this->recibosPDF($recibosDevolver, $empresa[0]);
							}
						}else{
							//No vienen recibos
							$this->retorno['error'] = '10';
						}
					}else{
						$this->retorno['error'] = '7';
					}
				}else{
					$this->retorno['error'] = '6';
				}
			break;
			case 'nc':
				if(isset($_GET['n'])&&isset($_GET['s'])){
					$sucursal = $_GET['s'];
					$consecutivo = $_GET['n'];
					if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){
						if($notaCreditoHead = $this->contabilidad->getNotaCreditoHeaderParaImpresion($consecutivo, $sucursal)){
							if($notaCreditoBody = $this->contabilidad->getArticulosNotaCreditoParaImpresion($consecutivo, $sucursal)){
															
								$total = 0;
								$subtotal = 0;
								$total_iva = 0;
								
								foreach($notaCreditoBody as $art){
									$total = $total + ($art->precio * ($art->bueno + $art->defectuoso));
									$total_iva = $total_iva + (($art->precio * ($art->bueno + $art->defectuoso)) * ($notaCreditoHead[0]->iva/100));
									$subtotal = $subtotal + (($art->precio * ($art->bueno + $art->defectuoso)) - (($art->precio * ($art->bueno + $art->defectuoso)) * ($notaCreditoHead[0]->iva/100)));
								}
								
								$notaCreditoHead[0]->total = $total;
								$notaCreditoHead[0]->subtotal = $subtotal;
								$notaCreditoHead[0]->total_iva = $total_iva;

								$this->notaCreditoPDF($empresa[0], $notaCreditoHead[0], $notaCreditoBody);
							}else{
								$this->retorno['error'] = '12';
							}
						}else{
							$this->retorno['error'] = '11';
						}					
					}else{
						$this->retorno['error'] = '7';
					}
				}else{
					$this->retorno['error'] = '6';
				}
			break;
			case 'nd':
				if(isset($_GET['n'])&&isset($_GET['s'])){
					$sucursal = $_GET['s'];
					$consecutivo = $_GET['n'];
					if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){
						if($notaDebitoHead = $this->contabilidad->getHeadNotaDebito($consecutivo, $sucursal)){
							if($notaDebitoBody = $this->contabilidad->getProductosNotaDebito($consecutivo, $sucursal)){
															
								$total = 0;
								$subtotal = 0;
								$total_iva = 0;
								
								foreach($notaDebitoBody as $art){
									$total = $total + ($art->precio * $art->cantidad);
									$total_iva = $total_iva + (($art->precio * $art->cantidad) * ($notaDebitoHead[0]->iva/100));
									$subtotal = $subtotal + (($art->precio * $art->cantidad) - (($art->precio * $art->cantidad) * ($notaDebitoHead[0]->iva/100)));
								}
								
								$notaDebitoHead[0]->total = $total;
								$notaDebitoHead[0]->subtotal = $subtotal;
								$notaDebitoHead[0]->total_iva = $total_iva;
								
								
								$this->notaDebitoPDF($empresa[0], $notaDebitoHead[0], $notaDebitoBody);
							}else{
								$this->retorno['error'] = '14';
							}
						}else{
							$this->retorno['error'] = '13';
						}					
					}else{
						$this->retorno['error'] = '7';
					}
				}else{
					$this->retorno['error'] = '6';
				}
			break;
			case 'p':
				if(isset($_GET['n'])&&isset($_GET['s'])){
					$sucursal = $_GET['s'];
					$consecutivo = $_GET['n'];
					if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){
						if($proformaHead = $this->proforma_m->getProformasHeadersImpresion($consecutivo, $sucursal)){
							if($proformaBody = $facturaPRODUCTS = $this->proforma_m->getArticulosProformaImpresion($consecutivo, $sucursal)){
								$this->proformaPDF($empresa, $proformaHead, $proformaBody);							
							}
						}				
					}
				}
			break;
			case 't':
				if(isset($_GET['n'])&&isset($_GET['s'])){
					$sucursal = $_GET['s'];
					$consecutivo = $_GET['n'];
					if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){
						if($traspaso = $this->contabilidad->getTraspasoArticulos($consecutivo)){
							if($productos = $this->contabilidad->getArticulosTraspaso($consecutivo)){
								$traspaso->nombre_salida = $this->empresa->getNombreEmpresa($traspaso->salida);
								$traspaso->nombre_entrada = $this->empresa->getNombreEmpresa($traspaso->entrada);
								$this->traspasoPDF($empresa, $traspaso, $productos);
							}
						}			
					}
				}
			break;
			case 'cc':
				if(isset($_GET['n'])&&isset($_GET['s'])){
					$sucursal = $_GET['s'];
					$consecutivo = $_GET['n'];
					if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){
						if($cierre = $this->contabilidad->getCierreCaja($consecutivo, $sucursal)){
							$fechaCierre = $cierre->fechaCruda;
							//Obtener la fecha del cierre que esta antes de este cierre
							$fechaCierreAnterior = $this->contabilidad->getFechaUltimoCierreCajaAntesDeCierreCaja($sucursal, $consecutivo);
							
							$facturas = $this->getPrimeraUltimaFactura($sucursal, $fechaCierre, $fechaCierreAnterior);
		
							$datos['primeraFactura'] = $facturas['primera'];
							$datos['ultimaFactura'] = $facturas['ultima'];
							
							$retirosParciales = $this->getRetirosParcialesYTotal($sucursal, $fechaCierre, $fechaCierreAnterior);
							
							$datos['retirosParciales'] = $retirosParciales['retiros'];
							$datos['totalRecibosParciales'] = $retirosParciales['total'];
								
							$datos['pagoDatafonos'] = $this->getPagosDatafonos($sucursal, $fechaCierre, $fechaCierreAnterior);
							
							$datos['pagoMixto'] = $this->obtenerPagosMixtos($sucursal, $fechaCierre, $fechaCierreAnterior);
							
							$datos['recibos'] = $this->obtenerRecibosDeDinero($sucursal, $fechaCierre, $fechaCierreAnterior);
							
							$datos['totalFacturasContado'] = $this->obtenerTotalFacturasContado($sucursal, $fechaCierre, $fechaCierreAnterior);
							
							$datos['totalCreditos'] = $this->obtenerTotalCreditos($sucursal, $fechaCierre, $fechaCierreAnterior);
							
							$datos['totalNotasCredito'] = $this->obtenerTotalesNotasCredito($sucursal, $fechaCierre, $fechaCierreAnterior);
							
							$datos['totalNotasDebito'] = $this->obtenerTotalesNotasDebito($sucursal, $fechaCierre, $fechaCierreAnterior);
							
							$datos['totalFacturasDeposito'] = $this->obtenerTotalFacturasDeposito($sucursal, $fechaCierre, $fechaCierreAnterior);
							
							$datos['vendedores'] = $this->obtenerVendidoPorCadaVendedor($sucursal, $fechaCierre, $fechaCierreAnterior);
							
							$cierre->datos = $datos;
							
							if($billetes = $this->contabilidad->getDenominacionesCierreCajaPorTipoYMoneda($consecutivo, 'billete', 'colones')){
								if($monedas = $this->contabilidad->getDenominacionesCierreCajaPorTipoYMoneda($consecutivo, 'moneda', 'colones')){
									if($dolares = $this->contabilidad->getDenominacionesCierreCajaPorTipoYMoneda($consecutivo, 'billete', 'dolares')){
										
										/*echo "<pre>";
										print_r($cierre);
										print_r($billetes);
										print_r($monedas);
										print_r($dolares);
										echo "</pre>";*/
										$this->cierreCajaPDF($empresa, $cierre, $billetes, $monedas, $dolares);
									}
								}
							}						
						}	
					}
				}
			break;
			default:
				$this->retorno['error'] = '5';
			break;
		}	
	}
	
	
	private function facturaPDF($empresa, $fhead, $fbody){
		require('/../libraries/fpdf/fpdf.php');
		$pdf = new FPDF('P','mm','A4');
		
		$cantidadProductos = sizeOf($fbody);
		$paginasADibujar = $this->paginasADibujar($cantidadProductos);
		$this->cantidadPaginas = $paginasADibujar + 1;
		
		while($paginasADibujar>=$this->numPagina){
			//Agregamos pag		
			$pdf->AddPage();			
			//Agregamos el encabezado
			$this->encabezadoDocumentoPDF('f', $empresa[0], $fhead[0], $pdf);
			//Agregamos Productos
			$inicio = $this->numPagina*33;
			if((($this->numPagina+1)*33)<$cantidadProductos){
				$final = ($this->numPagina+1)*33;
			}else{
				$final = $cantidadProductos;
			}
			
			$this->printProducts($fbody, $inicio, $final-1, $pdf, $fhead[0]);
			//Definimos el pie de pagina
			$this->pieDocumentoPDF('f', $fhead[0], $empresa[0], $pdf);
			$this->numPagina++;
		}
		//Imprimimos documento
		$pdf->Output();
	}
	
	private function proformaPDF($empresa, $fhead, $fbody){
		require('/../libraries/fpdf/fpdf.php');
		$pdf = new FPDF('P','mm','A4');
		
		$cantidadProductos = sizeOf($fbody);
		$paginasADibujar = $this->paginasADibujar($cantidadProductos);
		
		while($paginasADibujar>=$this->numPagina){
			//Agregamos pag		
			$pdf->AddPage();			
			//Agregamos el encabezado
			$this->encabezadoDocumentoPDF('p', $empresa[0], $fhead[0], $pdf);
			//Agregamos Productos
			$inicio = $this->numPagina*33;
			if((($this->numPagina+1)*33)<$cantidadProductos){
				$final = ($this->numPagina+1)*33;
			}else{
				$final = $cantidadProductos;
			}
			
			$this->printProducts($fbody, $inicio, $final-1, $pdf, $fhead[0]);
			//Definimos el pie de pagina
			$this->pieDocumentoPDF('p', $fhead[0], $empresa[0], $pdf);
			$this->numPagina++;
		}
		//Imprimimos documento
		$pdf->Output();
	}
	
	private function traspasoPDF($empresa, $fhead, $fbody){
		require('/../libraries/fpdf/fpdf.php');
		$pdf = new FPDF('P','mm','A4');
		
		$cantidadProductos = sizeOf($fbody);
		$paginasADibujar = $this->paginasADibujar($cantidadProductos);
		
		while($paginasADibujar>=$this->numPagina){
			//Agregamos pag		
			$pdf->AddPage();			
			//Agregamos el encabezado
			$this->encabezadoDocumentoPDF('t', $empresa[0], $fhead, $pdf);
			//Agregamos Productos
			$inicio = $this->numPagina*33;
			if((($this->numPagina+1)*33)<$cantidadProductos){
				$final = ($this->numPagina+1)*33;
			}else{
				$final = $cantidadProductos;
			}
			
			//$this->printProducts($fbody, $inicio, $final-1, $pdf);
			$this->printArticulosTraspaso($fbody, $inicio, $final-1, $pdf);
			//Definimos el pie de pagina
	//		$this->pieDocumentoPDF('p', $fhead[0], $empresa[0], $pdf);
			$this->numPagina++;
		}
		//Imprimimos documento
		$pdf->Output();
	}
	
	private function notaCreditoPDF($empresa, $head, $productos){
		require('/../libraries/fpdf/fpdf.php');
		$pdf = new FPDF('P','mm','A4');
		
		$cantidadProductos = sizeOf($productos);
		$paginasADibujar = $this->paginasADibujar($cantidadProductos);
		
		while($paginasADibujar>=$this->numPagina){
			//Agregamos pag		
			$pdf->AddPage();			
			//Agregamos el encabezado
			$this->encabezadoDocumentoPDF('nc', $empresa, $head, $pdf);
			//Agregamos Productos
			$inicio = $this->numPagina*33;
			if((($this->numPagina+1)*33)<$cantidadProductos){
				$final = ($this->numPagina+1)*33;
			}else{
				$final = $cantidadProductos;
			}
			
			$this->printProductsNotaCredito($productos, $inicio, $final-1, $pdf);
			//Definimos el pie de pagina
			$this->pieDocumentoPDF('nc', $head, $empresa, $pdf);
			$this->numPagina++;
		}
		//Imprimimos documento
		$pdf->Output();
	}
	
	private function notaDebitoPDF($empresa, $head, $productos){
		require('/../libraries/fpdf/fpdf.php');
		$pdf = new FPDF('P','mm','A4');
		
		$cantidadProductos = sizeOf($productos);
		$paginasADibujar = $this->paginasADibujar($cantidadProductos);
		
		while($paginasADibujar>=$this->numPagina){
			//Agregamos pag		
			$pdf->AddPage();			
			//Agregamos el encabezado
			$this->encabezadoDocumentoPDF('nd', $empresa, $head, $pdf);
			//Agregamos Productos
			$inicio = $this->numPagina*33;
			if((($this->numPagina+1)*33)<$cantidadProductos){
				$final = ($this->numPagina+1)*33;
			}else{
				$final = $cantidadProductos;
			}
			
			$this->printProductsNotaDebito($productos, $inicio, $final-1, $pdf);
			//Definimos el pie de pagina
			$this->pieDocumentoPDF('nd', $head, $empresa, $pdf);
			$this->numPagina++;
		}
		//Imprimimos documento
		$pdf->Output();
	}
	
	private function recibosPDF($recibos, $empresa){
		require('/../libraries/fpdf/fpdf.php');
		$pdf = new FPDF('P','mm','A4');
		foreach($recibos as $recibo){
			//Agregamos una pagina
			$pdf->AddPage();
			//Obtenemos el recibo
			$recibo = $recibo[0];
			//Agregamos el encabezado
			$this->encabezadoDocumentoPDF('r', $empresa, $recibo, $pdf);
			//Agregamos el cuerpo del recibo
			//Caja redondeada 1
			$pdf->RoundedRect(10, 67, 190, 28, 5, '124', 'D');
			$pdf->SetFont('Arial','',11);
			$pdf->Text(12, 72, 'Recibimos de: ');
			$pdf->Text(42, 72, $recibo->cliente_cedula." - ".$recibo->cliente_nombre);
			
			$V = new EnLetras(); 
			$con_letra = $V->ValorEnLetras($recibo->monto,$recibo->moneda); 			
			
			$pdf->Text(12, 79, 'La suma de: ');
			$pdf->Text(42, 79, $this->fn($recibo->monto)." - $con_letra");
			$pdf->Text(12, 86, 'Por concepto de abono a la factura: ');
			$pdf->Text(12, 93, 'Consecutivo # ');
			$pdf->Text(42, 93, $recibo->factura);
			$pdf->Text(62, 93, 'Emitida el ');
			$pdf->Text(84, 93, $recibo->fecha_expedicion);
			$pdf->Text(132, 93, 'Por el monto de ');
			$pdf->Text(164, 93, $this->fn($recibo->Saldo_inicial));
			
			//Divisores
			$pdf->Line(10, 74, 200, 74); //Primer divisor			
			$pdf->Line(10, 81, 200, 81); //Segundo divisor
			$pdf->Line(10, 88, 200, 88); //Tercer divisor
			//$pdf->Line(10, 95, 200, 95); //Cuarto divisor
			$pdf->Line(40, 67, 40, 81); //Primer divisor vertical
			//$pdf->Line(40, 88, 40, 95); //Segundo divisor vertical
			$pdf->Line(60, 88, 60, 95); //Tercer divisor vertical
			//$pdf->Line(82, 88, 82, 95); //Cuarto divisor vertical
			$pdf->Line(130, 88, 130, 95); //Quinto divisor vertical
			//$pdf->Line(162, 88, 162, 95); //Sexto divisor vertical
			
			//Definimos el pie de pagina
			$this->pieDocumentoPDF('r', $recibo, $empresa, $pdf);
			
			//Aumentamos la cantidad de paginas
			$this->numPagina++;
		}
		//Imprimimos documento
		$pdf->Output();
	}
	
	private function cierreCajaPDF($empresa, $cierre, $billetes, $monedas, $dolares){
		require('/../libraries/fpdf/fpdf.php');
		$pdf = new FPDF('P','mm','A4');
		$pdf->AddPage();
		$this->encabezadoDocumentoPDF('cc', $empresa[0], $cierre, $pdf);
		$pdf->Line(10, 35, 200, 35);
		$pdf->Text(12, 40, 'Primera Factura: '.$cierre->datos['primeraFactura']);
		$pdf->Text(90, 40, 'Última Factura: '.$cierre->datos['ultimaFactura']);
		$pdf->Text(163, 40, 'Base: '.$this->fn($cierre->base));
		$pdf->Line(10, 42, 200, 42);
		
		//DENOMINACIONES
		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY(10, 45);	
		$pdf->Cell(190,5,'Efectivo',0,0,'C');
		$pdf->ln(5);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(63.33,5,'Billetes',1,0,'C');
		$pdf->Cell(63.33,5,'Monedas',1,0,'C');
		$pdf->Cell(63.33,5,'Dolares',1,0,'C');
		$pdf->ln(5);
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(20,5,'Deno.',1,0,'C');
		$pdf->Cell(18,5,'Cant.',1,0,'C');
		$pdf->Cell(25.3,5,'Total',1,0,'C');
		$pdf->Cell(20,5,'Deno.',1,0,'C');
		$pdf->Cell(18,5,'Cant.',1,0,'C');
		$pdf->Cell(25.3,5,'Total',1,0,'C');
		$pdf->Cell(20,5,'Deno.',1,0,'C');
		$pdf->Cell(18,5,'Cant.',1,0,'C');
		$pdf->Cell(25.3,5,'Total',1,0,'C');
		$pdf->ln(5);
		$pdf->SetFont('Arial','',10);
		foreach($billetes as $billete){
			$total = $billete->denominacion * $billete->cantidad;
			$pdf->Cell(20,5,$billete->denominacion,1,0,'C');
			$pdf->Cell(18,5,$billete->cantidad,1,0,'C');
			$pdf->Cell(25.3,5,$this->fn($total),1,0,'R');
			$pdf->ln(5);
		}
		$pdf->SetXY(73.33, 60);
		foreach($monedas as $moneda){
			$total = $moneda->denominacion * $moneda->cantidad;
			$pdf->Cell(20,5,$moneda->denominacion,1,0,'C');
			$pdf->Cell(18,5,$moneda->cantidad,1,0,'C');
			$pdf->Cell(25.3,5,$this->fn($total),1,0,'R');
			$pdf->ln(5);
			$pdf->SetX(73.33);
		}
		$pdf->SetXY(136.66, 60);
		foreach($dolares as $dolar){
			$total = $dolar->denominacion * $dolar->cantidad;
			$pdf->Cell(20,5,$dolar->denominacion,1,0,'C');
			$pdf->Cell(18,5,$dolar->cantidad,1,0,'C');
			$pdf->Cell(25.3,5,$this->fn($total),1,0,'R');
			$pdf->ln(5);
			$pdf->SetX(136.66);
		}
		$pdf->Cell(63.33,5,'Tipo de Cambio: '.$this->fn($cierre->tipo),1,0,'R');
		$pdf->ln(5);
		$pdf->SetX(136.66);
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(63.33,5,'Total Conteo: '.$this->fn($cierre->conteo),1,0,'R');
		
		//Retiros parciales y datafonos
		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY(10, 92);	
		$pdf->Cell(95,5,'Retiros Parciales',0,0,'C');
		$pdf->Cell(95,5,'Datáfonos',0,0,'C');
		$pdf->ln(5);
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(10,5,'#',1,0,'C');
		$pdf->Cell(55,5,'Fecha y Hora',1,0,'C');
		$pdf->Cell(25,5,'Total',1,0,'C');
		$pdf->Cell(10,5,'',0,0,'C');
		$pdf->Cell(15,5,'Banco',1,0,'C');
		$pdf->Cell(25,5,'Comisión',1,0,'C');
		$pdf->Cell(25,5,'Retención',1,0,'C');
		$pdf->Cell(25,5,'Total',1,0,'C');
		$pdf->ln(5);
		$pdf->SetFont('Arial','',10);
			
			
			
		if(!$cierre->datos['retirosParciales']){
			$pdf->Cell(90,5,'No hay retiros parciales. . .',1,0,'C');			
			$pdf->ln(5);
		}else{
			foreach($cierre->datos['retirosParciales'] as $retiroP){
				$pdf->Cell(10,5,$retiroP->Id,0,0,'C');
				$pdf->Cell(55,5,$retiroP->Fecha_Hora,0,0,'C');
				$pdf->Cell(25,5,$this->fn($retiroP->Monto),0,0,'R');
				$pdf->ln(5);
				$pdf->SetX(10);
			}
			$pdf->SetXY(10, 102);
			for($i = 0; $i<8; $i++){
				$pdf->Cell(10,5,'',1,0,'C');
				$pdf->Cell(55,5,'',1,0,'C');
				$pdf->Cell(25,5,'',1,0,'R');
				$pdf->ln(5);
				$pdf->SetX(10);
			}
		}
		$pdf->Cell(65,5,'Total:',1,0,'R');
		$pdf->Cell(25,5,$this->fn($cierre->datos['totalRecibosParciales']),1,0,'R');
		
		$pdf->SetXY(110, 102);
		if(sizeOf($cierre->datos['pagoDatafonos']['datafonos'])==0){
			$pdf->Cell(90,5,'No hay pagos con datáfono. . .',1,0,'C');
			$pdf->ln(5);
			$pdf->SetX(110);
		}else{
			foreach($cierre->datos['pagoDatafonos']['datafonos'] as $datafono){
				$pdf->Cell(15,5,$datafono->Banco_Nombre,1,0,'C');
				$pdf->Cell(25,5,$this->fn($datafono->Total_Comision),1,0,'R');
				$pdf->Cell(25,5,$this->fn($datafono->Total_Retencion),1,0,'R');
				$pdf->Cell(25,5,$this->fn($datafono->Total),1,0,'R');
				$pdf->ln(5);
				$pdf->SetX(110);
			}
		}
		$pdf->Cell(15,5,'Totales:',1,0,'C');
		$pdf->Cell(25,5,$this->fn($cierre->datos['pagoDatafonos']['totalComision']),1,0,'R');
		$pdf->Cell(25,5,$this->fn($cierre->datos['pagoDatafonos']['totalRetencion']),1,0,'R');
		$pdf->Cell(25,5,$this->fn($cierre->datos['pagoDatafonos']['totalDatafonos']),1,0,'R');
				
		//Pagos Mixtos y Recibos por dinero
		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY(10, 149);	
		$pdf->Cell(95,5,'Pagos Mixtos',0,0,'C');
		$pdf->Cell(95,5,'Recibos Por Dinero',0,0,'C');
		$pdf->ln(5);
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(40,5,'Cant. Facturas',1,0,'C');
		$pdf->Cell(25,5,'Efectivo',1,0,'C');
		$pdf->Cell(25,5,'Tarjeta',1,0,'C');
		$pdf->Cell(10,5,'',0,0,'C');
		$pdf->Cell(23,5,'Contado',1,0,'C');
		$pdf->Cell(23,5,'Tarjeta',1,0,'C');
		$pdf->Cell(23,5,'Depósito',1,0,'C');
		$pdf->Cell(21,5,'Abonos',1,0,'C');
		$pdf->ln(5);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(40,5,$cierre->datos['pagoMixto']['cantidadFacturas'],1,0,'C');
		$pdf->Cell(25,5,$this->fn($cierre->datos['pagoMixto']['efectivo']),1,0,'R');
		$pdf->Cell(25,5,$this->fn($cierre->datos['pagoMixto']['tarjeta']),1,0,'R');
		$pdf->Cell(10,5,'',0,0,'C');
		$pdf->Cell(23,5,$this->fn($cierre->datos['recibos']['efectivo']),1,0,'R');
		$pdf->Cell(23,5,$this->fn($cierre->datos['recibos']['tarjeta']),1,0,'R');
		$pdf->Cell(23,5,$this->fn($cierre->datos['recibos']['deposito']),1,0,'R');
		$pdf->Cell(21,5,$this->fn($cierre->datos['recibos']['abonos']),1,0,'R');
		$pdf->ln(5);
		$pdf->Cell(65,5,'Total:',1,0,'R');
		$pdf->Cell(25,5,$this->fn($cierre->datos['pagoMixto']['total']),1,0,'R');
		$pdf->Cell(10,5,'',0,0,'C');
		$pdf->Cell(65,5,'Total:',1,0,'R');
		$pdf->Cell(25,5,$this->fn($cierre->datos['recibos']['total']),1,0,'R');
		
		//Otros totales
		$baseDeCaja = $cierre->base;
		$totalRetirosParciales = $cierre->datos['totalRecibosParciales'];
		$retiroFinal = $cierre->conteo;
		$efectivoTotal = $baseDeCaja + $totalRetirosParciales + $retiroFinal;
		
		$pdf->SetFont('Arial','B',14);
		$pdf->ln(8);
		$pdf->Cell(190,5,'Otros Totales',0,0,'C');
		$pdf->ln(5);
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(47.5,5,'Facturas de Contado',1,0,'C');
		$pdf->Cell(47.5,5,'Efectivo',1,0,'C');
		$pdf->Cell(47.5,5,'Tarjetas',1,0,'C');
		$pdf->Cell(47.5,5,'Créditos',1,0,'C');
		$pdf->ln(5);
		$pdf->Cell(47.5,5,$this->fn($cierre->datos['totalFacturasContado']),1,0,'C');
		$pdf->Cell(47.5,5,$this->fn($efectivoTotal),1,0,'C');
		$pdf->Cell(47.5,5,$this->fn($cierre->datos['pagoDatafonos']['totalDatafonos']),1,0,'C');
		$pdf->Cell(47.5,5,$this->fn($cierre->datos['totalCreditos']['totalCredito']),1,0,'C');
		
		
		$pdf->ln(5);
		$pdf->Cell(47.5,5,'Encomiendas (Depos.)',1,0,'C');
		$pdf->Cell(47.5,5,'Apartados',1,0,'C');
		$pdf->Cell(47.5,5,'Notas Crédito',1,0,'C');
		$pdf->Cell(47.5,5,'Notas Débito',1,0,'C');
		$pdf->ln(5);
		$pdf->Cell(47.5,5,$this->fn($cierre->datos['totalFacturasDeposito']),1,0,'C');
		$pdf->Cell(47.5,5,$this->fn($cierre->datos['totalCreditos']['totalApartado']),1,0,'C');
		$pdf->Cell(47.5,5,$this->fn($cierre->datos['totalNotasCredito']['total']),1,0,'C');
		$pdf->Cell(47.5,5,$this->fn($cierre->datos['totalNotasDebito']['total']),1,0,'C');
		
		//Vendedores
		$pdf->SetFont('Arial','B',14);
		$pdf->ln(8);
		$pdf->Cell(190,5,'Vendedores',0,0,'C');
		$pdf->ln(5);
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(70,5,'Vendedor',1,0,'C');
		$pdf->Cell(25,5,'Vendido',1,0,'C');
		$pdf->Cell(70,5,'Vendedor',1,0,'C');
		$pdf->Cell(25,5,'Vendido',1,0,'C');
		$pdf->ln(5);
		for($i = 0;$i<8;$i++){
			$pdf->Cell(70,5,'',1,0,'C');
			$pdf->Cell(25,5,'',1,0,'C');
			$pdf->Cell(70,5,'',1,0,'C');
			$pdf->Cell(25,5,'',1,0,'C');
			$pdf->ln(5);
		}
		$pdf->SetXY(10, 210);
		$contador = 1;			
		foreach($cierre->datos['vendedores']['vendidoVendedores'] as $vendedor){
			if($contador <= 8){
				$pdf->Cell(70,5,$vendedor[0]->usuario,0,0,'C');
				$pdf->Cell(25,5,$this->fn($vendedor[0]->total_vendido),0,0,'C');
				$pdf->ln(5);
			}
			if($contador == 9){
				$pdf->SetXY(105,195);
			}
			if($contador > 8){
				$pdf->Cell(70,5,$vendedor[0]->usuario,0,0,'C');
				$pdf->Cell(25,5,$this->fn($vendedor[0]->total_vendido),0,0,'C');
				$pdf->ln(5);
				$pdf->SetX(105);
			}
			$contador++;
		}
		$pdf->SetFont('Arial','B',11);
		$pdf->SetXY(10,250);
		$pdf->Cell(190,5,'Total Vendedores: '.$this->fn($cierre->datos['vendedores']['totalVendido']),1,0,'R');
		
		//Realizado Por:
		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY(10,270);
		$pdf->Cell(95,5,'Realizado por: '.$cierre->usuario,0,0,'R');
		//$pdf->ln(10);
		//$pdf->SetX(80);
		$pdf->Cell(95,5,'Firma: _______________________',0,0,'R');
		
		//Imprimimos documento
		$pdf->Output();
	}
	
	private function encabezadoDocumentoPDF($tipo, $empresa, $encabezado, &$pdf){
		//var_dump($empresa);
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell(40,10,$empresa->nombre);
		$pdf->Line(10, 17, 100, 17);
		$pdf->ln(5);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(40,10,'Cédula Jurídica: '.$empresa->cedula);
		$pdf->ln(4);
		$pdf->Cell(40,10,'Teléfono: '.$empresa->telefono);
		$pdf->ln(4);
		$pdf->Cell(40,10,'Email: '.$empresa->email);
		switch($tipo){
			case 'f':
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Factura #'.$encabezado->consecutivo);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha);
				$pdf->SetFont('Arial','',11);
				$pdf->Text(172, 16, 'Pag. # '.($this->numPagina+1)." de ".$this->cantidadPaginas);
				
				//Info del cliente
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(12, 42, 'Cliente');
				$pdf->SetFont('Arial','',11);
				$pdf->Text(12, 49, 'Identificación: '.$encabezado->cliente_ced);
				$pdf->SetXY(11, 50);
				$pdf->MultiCell(89, 5, 'Nombre: '.$encabezado->cliente_nom);
				//Caja redondeada 1
				$pdf->RoundedRect(10, 37, 190, 23, 5, '1234', 'D');
				//Divisores				
				//$pdf->Line(10, 37, 10, 60); //Lado izquierdo borde
				$pdf->Line(100, 37, 100, 60); //Centro caja
				//$pdf->Line(10, 60, 200, 60); //Borde de abajo
				//$pdf->Line(200, 37, 200, 60); //Lado derecho caja
				//$pdf->Line(10, 37, 200, 37); //Borde de arriba
				$pdf->Line(10, 44, 200, 44); //Borde debajo cliente y descripcion
				$pdf->Line(100, 55, 200, 55); //Borde arriba vendedor
				//Info de la factura
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(102, 42, 'Descripción');
				$pdf->SetFont('Arial','',11);
				$pdf->Text(102, 49, 'Tipo: '.$encabezado->tipo);
				$pdf->Text(102, 54, 'Moneda: '.$encabezado->moneda);
				$pdf->Text(102, 59, 'Vendedor: '.$encabezado->vendedor);
				switch($encabezado->tipo){
					case 'credito':
						$pdf->Text(140, 49, 'Días: '.$encabezado->diasCredito);
						$pdf->Text(140, 54, 'Vence: '.$encabezado->fechaVencimiento);
					break;
					case 'mixto':
						$pdf->Text(140, 49, 'Pago Tarjeta: '.$this->fn($encabezado->cantidadTarjeta));
						$pdf->Text(140, 54, 'Pago Contado: '.$this->fn($encabezado->cantidadContado));
					break;
					case 'apartado':
						$pdf->Text(140, 49, 'Abono: '.$this->fn($encabezado->abono));
					break;
				}
			break;
			case 'nc':
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Nota Crédito #'.$encabezado->nota);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha);
				
				$pdf->Text(180, 27, 'Pag. # '.($this->numPagina+1));
				
				//Info del cliente
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(12, 42, 'Cliente');
				$pdf->SetFont('Arial','',11);
				$pdf->Text(12, 49, 'Identificación: '.$encabezado->cliente_cedula);
				$pdf->SetXY(11, 50);
				$pdf->MultiCell(89, 5, 'Nombre: '.$encabezado->cliente_nombre);
				//Caja redondeada 1
				$pdf->RoundedRect(10, 37, 190, 23, 5, '1234', 'D');
				//Divisores					
				$pdf->Line(100, 37, 100, 60); //Centro caja				
				$pdf->Line(10, 44, 200, 44); //Borde debajo cliente y descripcion
				$pdf->Line(100, 51, 200, 51); //Borde arriba vendedor
				//Info de la factura
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(102, 42, 'Descripción');
				$pdf->Text(102, 57, 'Esta nota crédito se aplica a la factura #'.$encabezado->factura_aplicar);
				$pdf->SetFont('Arial','',11);
				$pdf->Text(102, 49, 'Moneda: '.$encabezado->moneda);
				//$pdf->Text(102, 59, 'Vendedor: '.$encabezado->vendedor);
			break;
			case 'nd':
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Nota Débito #'.$encabezado->nota);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha);
				
				$pdf->Text(180, 27, 'Pag. # '.($this->numPagina+1));
								
			break;
			case 'p':
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Proforma #'.$encabezado->consecutivo);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha);
				
				$pdf->Text(180, 27, 'Pag. # '.($this->numPagina+1));
				
				//Info del cliente
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(12, 42, 'Cliente');
				$pdf->SetFont('Arial','',11);
				$pdf->Text(12, 49, 'Identificación: '.$encabezado->cliente_ced);
				$pdf->SetXY(11, 50);
				$pdf->MultiCell(89, 5, 'Nombre: '.$encabezado->cliente_nom);
				//Caja redondeada 1
				$pdf->RoundedRect(10, 37, 190, 23, 5, '1234', 'D');
				//Divisores				
				$pdf->Line(100, 37, 100, 60); //Centro caja
				$pdf->Line(10, 44, 200, 44); //Borde debajo cliente y descripcion
				$pdf->Line(100, 55, 200, 55); //Borde arriba vendedor
				//Info de la factura
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(102, 42, 'Descripción');
				$pdf->SetFont('Arial','',11);
				$pdf->Text(102, 49, 'Moneda: '.$encabezado->moneda);
				$pdf->Text(102, 59, 'Vendedor: '.$encabezado->vendedor);				
			break;
			case 'r':
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Recibo de Dinero #'.$encabezado->recibo);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha_recibo);
				
				$pdf->Text(180, 27, 'Pag. # '.($this->numPagina+1));
				
				//Info del cliente
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(12, 42, 'Cliente');
				$pdf->SetFont('Arial','',11);
				$pdf->Text(12, 49, 'Identificación: '.$encabezado->cliente_cedula);
				$pdf->SetXY(11, 50);
				$pdf->MultiCell(89, 5, 'Nombre: '.$encabezado->cliente_nombre);
				//Caja redondeada 1
				$pdf->RoundedRect(10, 37, 190, 23, 5, '1234', 'D');
				//Divisores				
				$pdf->Line(100, 37, 100, 60); //Centro caja
				$pdf->Line(10, 44, 200, 44); //Borde debajo cliente y descripcion
				$pdf->Line(100, 55, 200, 55); //Borde arriba vendedor
				//Info de la factura
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(102, 42, 'Descripción');
				$pdf->SetFont('Arial','',11);
				$pdf->Text(102, 49, 'Moneda: '.$encabezado->moneda);
				$pdf->Text(102, 54, 'Tipo de Pago: '.$encabezado->tipo_pago);
			break;
			case 't':
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Traspaso de Artículos #'.$encabezado->consecutivo);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha);
				
				$pdf->Text(180, 27, 'Pag. # '.($this->numPagina+1));
				
				//Info del traspaso
				$pdf->SetFont('Arial','',11);
				$pdf->Text(12, 42, 'Suc. Salida:');		
				$pdf->Text(42, 42, $encabezado->salida." - ".$encabezado->nombre_salida);	
				$pdf->Text(12, 49, 'Suc. Entrada:');	
				$pdf->Text(42, 49, $encabezado->entrada." - ".$encabezado->nombre_entrada);
				//Caja redondeada 1
				$pdf->RoundedRect(10, 37, 190, 14, 2, '1234', 'D');
				//Divisores				
				$pdf->Line(110, 37, 110, 51); //Centro caja
				$pdf->Line(150, 37, 150, 44); //Despues de factura aplicada
				$pdf->Line(135, 44, 135, 51); //Despues de realizador
				$pdf->Line(40, 37, 40, 51); //Izquierda caja
				$pdf->Line(10, 44, 200, 44); //Borde debajo cliente y descripcion
			
				$pdf->Text(112, 42, 'Factura Traspasada:');
				$pdf->Text(152, 42, $encabezado->factura);
				$pdf->Text(112, 49, 'Realizador:');
				$pdf->Text(137, 49, $encabezado->usuario." - ".$encabezado->usuario_nombre);				
			break;
			case 'cc':
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Cierre de Caja #'.$encabezado->consecutivo);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha);
				
				$pdf->Text(180, 27, 'Pag. # '.($this->numPagina+1));
								
			break;
		}
	}
	
	private function pieDocumentoPDF($tipo, $encabezado, $empresa, &$pdf){
		switch($tipo){
			case 'f':
				//Parte de observaciones
				$this->observaciones($encabezado->observaciones, $pdf);
				//Leyenda de tributacion
				$pdf->SetFont('Arial','',8);
				$pdf->SetXY(10, 270);	
				$pdf->MultiCell(190,3,$empresa->leyenda,0,'C');
				//Costos totales
				$subtotal = $encabezado->subtotal;
				$totalIVA = $encabezado->total_iva;
				$total = $encabezado->total;
				$retencion = $encabezado->retencion;
				//Valoramos si es en dolares
				if($encabezado->moneda=='dolares'){
					$subtotal = $subtotal/$encabezado->cambio;
					$totalIVA = $totalIVA/$encabezado->cambio;
					$total = $total/$encabezado->cambio;
					$retencion = $retencion/$encabezado->cambio;
				}
				$pdf->SetFont('Arial','',11);
				$pdf->SetXY(131, 240);	
				$pdf->Cell(41,7,'Subtotal:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($subtotal),1,0,'R');
				$pdf->SetXY(131, 247);	
				$pdf->Cell(41,7,'IVA:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($totalIVA),1,0,'R');
				$pdf->SetXY(131, 254);	
				$pdf->Cell(41,7,'Retención:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($retencion),1,0,'R');
				$pdf->SetXY(131, 261);	
				$pdf->Cell(41,7,'Total:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($total),1,0,'R');
			break;			
			case 'nc':
				//Parte de observaciones
				$this->observaciones('', $pdf);
				//Costos totales
				$pdf->SetFont('Arial','',11);
				$pdf->SetXY(131, 240);	
				$pdf->Cell(41,7,'Subtotal:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($encabezado->subtotal),1,0,'R');
				$pdf->SetXY(131, 247);	
				$pdf->Cell(41,7,'IVA:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($encabezado->total_iva),1,0,'R');
				$pdf->SetXY(131, 254);	
				$pdf->Cell(41,7,'Total:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($encabezado->total),1,0,'R');
			break;
			case 'nd':
				//Parte de observaciones
				$this->observaciones('', $pdf);
				//Costos totales
				$pdf->SetFont('Arial','',11);
				$pdf->SetXY(131, 240);	
				$pdf->Cell(41,7,'Subtotal:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($encabezado->subtotal),1,0,'R');
				$pdf->SetXY(131, 247);	
				$pdf->Cell(41,7,'IVA:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($encabezado->total_iva),1,0,'R');
				$pdf->SetXY(131, 254);	
				$pdf->Cell(41,7,'Total:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($encabezado->total),1,0,'R');
			break;
			case 'p':
				//Parte de observaciones
				$this->observaciones($encabezado->observaciones, $pdf);
				//Leyenda de tributacion
				$pdf->SetFont('Arial','',8);
				$pdf->SetXY(10, 270);	
				$pdf->MultiCell(190,3,$empresa->leyenda,0,'C');
				//Costos totales
				$subtotal = $encabezado->subtotal;
				$totalIVA = $encabezado->total_iva;
				$total = $encabezado->total;
				$retencion = $encabezado->retencion;
				//Valoramos si es en dolares
				if($encabezado->moneda=='dolares'){
					$subtotal = $subtotal/$encabezado->cambio;
					$totalIVA = $totalIVA/$encabezado->cambio;
					$total = $total/$encabezado->cambio;
					$retencion = $retencion/$encabezado->cambio;
				}
				$pdf->SetFont('Arial','',11);
				$pdf->SetXY(131, 240);	
				$pdf->Cell(41,7,'Subtotal:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($subtotal),1,0,'R');
				$pdf->SetXY(131, 247);	
				$pdf->Cell(41,7,'IVA:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($totalIVA),1,0,'R');
				$pdf->SetXY(131, 254);	
				$pdf->Cell(41,7,'Retención:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($retencion),1,0,'R');
				$pdf->SetXY(131, 261);	
				$pdf->Cell(41,7,'Total:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($total),1,0,'R');
			break;
			case 'r':
				//Costos totales
				$pdf->SetFont('Arial','',11);
				$pdf->SetXY(130, 95);	
				$pdf->Cell(42,7,'Saldo Anterior:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($encabezado->saldo_anterior),1,0,'R');
				$pdf->SetXY(130, 102);	
				$pdf->Cell(42,7,'Este Abono:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($encabezado->monto),1,0,'R');
				$pdf->SetXY(130, 109);	
				$pdf->Cell(42,7,'Saldo Actual:',1,0,'R');
				$pdf->Cell(28,7,$this->fn($encabezado->saldo),1,0,'R');
			break;
		}		
	}
	
	private function fn($numero){		
		return number_format($numero,$this->configuracion->getDecimales());
	}
	
	private function fe($valor){
		if($valor){
			return 'E';
		}else{
			return ' ';
		}
	}
	
	private function paginasADibujar($productos){
		$aux = $productos / 33; // 33 es el maximo de productos por pagina
		$auxInteger = intval($aux);
		if($auxInteger<$aux){
			return $auxInteger++;
		}elseif($auxInteger==$aux){
			return $auxInteger;
		}
		$this->cantidadPaginas = $auxInteger+1;
		return $auxInteger;
	}
	
	private function observaciones($obs, &$pdf){
		//Agregamos el cuadro de observaciones
		$pdf->SetFont('Arial','B',12);
		$pdf->Text(11, 245, 'Observaciones:');
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY(10, 246);	
		$pdf->MultiCell(100,5,$obs);
	}
	
	private function printProducts($productos, $inicio, $fin, &$pdf, $fhead){
		//Agregamos el apartado de productos
		$pdf->SetFont('Arial','B',12);
		$pdf->Text(90, 65, 'Productos');
		//Caja redondeada 1
		$pdf->RoundedRect(10, 67, 190, 173, 5, '12', 'D');
		//Divisores verticales de productos
		$pdf->Line(10, 74, 200, 74);		
		//$pdf->Line(10, 67, 200, 67); //Borde abajo productos
		//$pdf->Line(10, 60, 10, 240); //Borde lado izquierdo tabla
		$pdf->Line(30, 67, 30, 240); //Divisor de codigo y descripcion
		$pdf->Line(110, 67, 110, 240); //Divisor de descripcion y cantidad
		$pdf->Line(125, 67, 125, 240); //Divisor de cantidad y exento
		$pdf->Line(131, 67, 131, 240); //Divisor de exento y descuento
		$pdf->Line(145, 67, 145, 240); //Divisor de descuento y precio unitario
		$pdf->Line(172, 67, 172, 240); //Divisor de precio unitario y precio total		
		//$pdf->Line(200, 60, 200, 240); //Borde lado derecho tabla
		//$pdf->Line(10, 240, 200, 240); //Borde abajo productos
		//Encabezado de productos
		$pdf->SetFont('Arial','',10);
		$pdf->Text(13, 72, 'Código');
		$pdf->Text(58, 72, 'Descripción');
		$pdf->Text(112, 72, 'Cant.');
		$pdf->Text(126.5, 72, 'E');
		$pdf->Text(133, 72, 'Desc.');
		$pdf->Text(149, 72, 'P/Unitario');
		$pdf->Text(179, 72, 'P/Total');
		//Agregamos Productos
		$pdf->SetFont('Arial','',8);
		
		$pdf->SetXY(110, 75.3);		
		$sl = 5; //Salto de linea
		$pl = 79; //Primera linea
		
		
		for($cc = $inicio; $cc<=$fin; $cc++){
			//Calculamos precio total con descuento
			$total = $productos[$cc]->cantidad * ($productos[$cc]->precio - ($productos[$cc]->precio * ($productos[$cc]->descuento/100))); 
			$precio = $productos[$cc]->precio;
			//Valoramos si es en dolares
			if($fhead->moneda=='dolares'){
				$total = $total/$fhead->cambio;
				$precio = $precio/$fhead->cambio;
			}
			
			$pdf->Text(11, $pl, $productos[$cc]->codigo);
			$pdf->Text(31, $pl, substr($productos[$cc]->descripcion,0,33));
			$pdf->cell(15,5,$productos[$cc]->cantidad,0,0,'C');
			$pdf->cell(6,5,$this->fe($productos[$cc]->exento),0,0,'C');
			$pdf->cell(14,5,$this->fn($productos[$cc]->descuento),0,0,'C');
			$pdf->cell(27.5,5,$this->fn($precio),0,0,'R');
			$pdf->cell(28,5,$this->fn($total),0,0,'R');			
			$pdf->ln($sl);
			$pdf->SetX(110);
			$pl += $sl;
		}
		
		// MAXIMO 33 PRODUCTOS POR PAGINA
		
		/*for($cc = 0; $cc<33; $cc++){
			$pdf->Text(11, $pl, $cc);
			$pl += $sl;
		}*/
	}
	
	private function printProductsNotaCredito($productos, $inicio, $fin, &$pdf){
		//Agregamos el apartado de productos
		$pdf->SetFont('Arial','B',12);
		$pdf->Text(90, 65, 'Productos');
		//Caja redondeada 1
		$pdf->RoundedRect(10, 67, 190, 173, 5, '12', 'D');
		//Divisores verticales de productos
		$pdf->Line(10, 74, 200, 74);		
		//$pdf->Line(10, 67, 200, 67); //Borde abajo productos
		//$pdf->Line(10, 60, 10, 240); //Borde lado izquierdo tabla
		$pdf->Line(30, 67, 30, 240); //Divisor de codigo y descripcion
		$pdf->Line(110, 67, 110, 240); //Divisor de descripcion y cantidad
		$pdf->Line(125, 67, 125, 240); //Divisor de cantidad y exento
		//$pdf->Line(131, 67, 131, 240); //Divisor de exento y descuento
		$pdf->Line(145, 67, 145, 240); //Divisor de descuento y precio unitario
		$pdf->Line(172, 67, 172, 240); //Divisor de precio unitario y precio total		
		//$pdf->Line(200, 60, 200, 240); //Borde lado derecho tabla
		//$pdf->Line(10, 240, 200, 240); //Borde abajo productos
		//Encabezado de productos
		$pdf->SetFont('Arial','',10);
		$pdf->Text(13, 72, 'Código');
		$pdf->Text(58, 72, 'Descripción');
		$pdf->Text(112, 72, 'Bueno');
		$pdf->Text(126.5, 72, 'Defectuoso');
		$pdf->Text(151, 72, 'P/Unitario');
		$pdf->Text(179, 72, 'P/Total');
		//Agregamos Productos
		$pdf->SetFont('Arial','',8);
		
		$pdf->SetXY(110, 75.3);		
		$sl = 5; //Salto de linea
		$pl = 79; //Primera linea
		
		
		for($cc = $inicio; $cc<=$fin; $cc++){
			//Calculamos la cantidad
			$cantidad = $productos[$cc]->bueno + $productos[$cc]->defectuoso;
			
			//Calculamos precio total con descuento
			$total = $cantidad * $productos[$cc]->precio; 
			
			$pdf->Text(11, $pl, $productos[$cc]->codigo);
			$pdf->Text(31, $pl, substr($productos[$cc]->descripcion,0,33));
			$pdf->cell(15,5,$productos[$cc]->bueno,0,0,'C');
			$pdf->cell(20,5,$productos[$cc]->defectuoso,0,0,'C');
			$pdf->cell(27.5,5,$this->fn($productos[$cc]->precio),0,0,'R');
			$pdf->cell(28,5,$this->fn($total),0,0,'R');			
			$pdf->ln($sl);
			$pdf->SetX(110);
			$pl += $sl;
		}
		
		// MAXIMO 33 PRODUCTOS POR PAGINA
		
		/*for($cc = 0; $cc<33; $cc++){
			$pdf->Text(11, $pl, $cc);
			$pl += $sl;
		}*/
	}
	
	private function printProductsNotaDebito($productos, $inicio, $fin, &$pdf){
		//Agregamos el apartado de productos
		$pdf->SetFont('Arial','B',12);
		$pdf->Text(90, 65, 'Productos');
		//Caja redondeada 1
		$pdf->RoundedRect(10, 67, 190, 173, 5, '12', 'D');
		//Divisores verticales de productos
		$pdf->Line(10, 74, 200, 74);		
		//$pdf->Line(10, 67, 200, 67); //Borde abajo productos
		//$pdf->Line(10, 60, 10, 240); //Borde lado izquierdo tabla
		$pdf->Line(30, 67, 30, 240); //Divisor de codigo y descripcion
		$pdf->Line(125, 67, 125, 240); //Divisor de descripcion y cantidad
		//$pdf->Line(125, 67, 125, 240); //Divisor de cantidad y exento
		//$pdf->Line(131, 67, 131, 240); //Divisor de exento y descuento
		$pdf->Line(145, 67, 145, 240); //Divisor de descuento y precio unitario
		$pdf->Line(172, 67, 172, 240); //Divisor de precio unitario y precio total		
		//$pdf->Line(200, 60, 200, 240); //Borde lado derecho tabla
		//$pdf->Line(10, 240, 200, 240); //Borde abajo productos
		//Encabezado de productos
		$pdf->SetFont('Arial','',10);
		$pdf->Text(13, 72, 'Código');
		$pdf->Text(58, 72, 'Descripción');
		$pdf->Text(128, 72, 'Cantidad');
		$pdf->Text(151, 72, 'P/Unitario');
		$pdf->Text(179, 72, 'P/Total');
		//Agregamos Productos
		$pdf->SetFont('Arial','',8);
		
		$pdf->SetXY(125, 75.3);		
		$sl = 5; //Salto de linea
		$pl = 79; //Primera linea
		
		
		for($cc = $inicio; $cc<=$fin; $cc++){
			//Calculamos precio total con descuento
			$total = $productos[$cc]->cantidad * $productos[$cc]->precio; 
			
			$pdf->Text(11, $pl, $productos[$cc]->codigo);
			$pdf->Text(31, $pl, substr($productos[$cc]->descripcion,0,33));
			$pdf->cell(20,5,$productos[$cc]->cantidad,0,0,'C');
			$pdf->cell(27.5,5,$this->fn($productos[$cc]->precio),0,0,'R');
			$pdf->cell(28,5,$this->fn($total),0,0,'R');			
			$pdf->ln($sl);
			$pdf->SetX(125);
			$pl += $sl;
		}
		
		// MAXIMO 33 PRODUCTOS POR PAGINA
		
		/*for($cc = 0; $cc<33; $cc++){
			$pdf->Text(11, $pl, $cc);
			$pl += $sl;
		}*/
	}
	
	private function printArticulosTraspaso($productos, $inicio, $fin, &$pdf){
		//Agregamos el apartado de productos
		$pdf->SetFont('Arial','B',12);
		$pdf->Text(90, 65, 'Productos');
		//Caja redondeada 1
		$pdf->RoundedRect(10, 67, 190, 173, 5, '12', 'D');
		//Divisores verticales de productos
		$pdf->Line(10, 74, 200, 74);		
		//$pdf->Line(10, 67, 200, 67); //Borde abajo productos
		//$pdf->Line(10, 60, 10, 240); //Borde lado izquierdo tabla
		$pdf->Line(30, 67, 30, 240); //Divisor de codigo y descripcion
		//$pdf->Line(125, 67, 125, 240); //Divisor de descripcion y cantidad
		//$pdf->Line(125, 67, 125, 240); //Divisor de cantidad y exento
		//$pdf->Line(131, 67, 131, 240); //Divisor de exento y descuento
		//$pdf->Line(145, 67, 145, 240); //Divisor de descuento y precio unitario
		$pdf->Line(172, 67, 172, 240); //Divisor de precio unitario y precio total		
		//$pdf->Line(200, 60, 200, 240); //Borde lado derecho tabla
		//$pdf->Line(10, 240, 200, 240); //Borde abajo productos
		//Encabezado de productos
		$pdf->SetFont('Arial','',10);
		$pdf->Text(13, 72, 'Código');
		$pdf->Text(58, 72, 'Descripción');
		$pdf->Text(179, 72, 'Cantidad');
		//Agregamos Productos
		$pdf->SetFont('Arial','',8);
		
		$pdf->SetXY(125, 75.3);		
		$sl = 5; //Salto de linea
		$pl = 79; //Primera linea
		
		
		for($cc = $inicio; $cc<=$fin; $cc++){
			//Calculamos precio total con descuento
		//	$total = $productos[$cc]->cantidad * $productos[$cc]->precio; 
			
			$pdf->Text(11, $pl, $productos[$cc]->Codigo);
			$pdf->Text(31, $pl, substr($productos[$cc]->Descripcion,0,33));
			$pdf->cell(20,5,' ',0,0,'C');
			$pdf->cell(27.5,5,' ',0,0,'R');
			$pdf->cell(28,5,$productos[$cc]->Cantidad,0,0,'C');			
			$pdf->ln($sl);
			$pdf->SetX(125);
			$pl += $sl;
		}
		
		// MAXIMO 33 PRODUCTOS POR PAGINA
		
		/*for($cc = 0; $cc<33; $cc++){
			$pdf->Text(11, $pl, $cc);
			$pl += $sl;
		}*/
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
		$pagos = $this->contabilidad->getPagosMixtosPorRangoFecha($sucursal, $fechaUltimoCierra, $fechaHoraActual);
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
		if($facturas = $this->contabilidad->getFacturasContadoPorRangoFecha($sucursal, $fechaUltimoCierra, $fechaHoraActual)){
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
		$total = 0;
		$subtotal = 0;
		$total_iva = 0;
		if($notas = $this->contabilidad->getNotaCreditoPorRangoFecha($sucursal, $fechaUltimoCierra, $fechaHoraActual)){
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
		if($notas = $this->contabilidad->getNotaDebitoPorRangoFecha($sucursal, $fechaUltimoCierra, $fechaHoraActual)){
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
	
	
	
	
	
	
	
	
}// FIN DE LA CLASE





class EnLetras 
{ 
  var $Void = ""; 
  var $SP = " "; 
  var $Dot = "."; 
  var $Zero = "0"; 
  var $Neg = "Menos"; 
   
function ValorEnLetras($x, $Moneda )  
{ 
    $s=""; 
    $Ent=""; 
    $Frc=""; 
    $Signo=""; 
         
    if(floatVal($x) < 0) 
     $Signo = $this->Neg . " "; 
    else 
     $Signo = ""; 
     
    if(intval(number_format($x,2,'.','') )!=$x) //<- averiguar si tiene decimales 
      $s = number_format($x,2,'.',''); 
    else 
      $s = number_format($x,2,'.',''); 
        
    $Pto = strpos($s, $this->Dot); 
         
    if ($Pto === false) 
    { 
      $Ent = $s; 
      $Frc = $this->Void; 
    } 
    else 
    { 
      $Ent = substr($s, 0, $Pto ); 
      $Frc =  substr($s, $Pto+1); 
    } 

    if($Ent == $this->Zero || $Ent == $this->Void) 
       $s = "Cero "; 
    elseif( strlen($Ent) > 7) 
    { 
       $s = $this->SubValLetra(intval( substr($Ent, 0,  strlen($Ent) - 6))) .  
             "Millones " . $this->SubValLetra(intval(substr($Ent,-6, 6))); 
    } 
    else 
    { 
      $s = $this->SubValLetra(intval($Ent)); 
    } 

    if (substr($s,-9, 9) == "Millones " || substr($s,-7, 7) == "Millón ") 
       $s = $s . "de "; 

    $s = $s . $Moneda; 

    /*if($Frc != $this->Void) 
    { 
       $s = $s . " " . $Frc. "/100"; 
       //$s = $s . " " . $Frc . "/100"; 
    } 
    $letrass=$Signo . $s . " M.N."; */
    return ($Signo . $s ); 
    
} 


function SubValLetra($numero)  
{ 
    $Ptr=""; 
    $n=0; 
    $i=0; 
    $x =""; 
    $Rtn =""; 
    $Tem =""; 

    $x = trim("$numero"); 
    $n = strlen($x); 

    $Tem = $this->Void; 
    $i = $n; 
     
    while( $i > 0) 
    { 
       $Tem = $this->Parte(intval(substr($x, $n - $i, 1).  
                           str_repeat($this->Zero, $i - 1 ))); 
       If( $Tem != "Cero" ) 
          $Rtn .= $Tem . $this->SP; 
       $i = $i - 1; 
    } 

     
    //--------------------- GoSub FiltroMil ------------------------------ 
    $Rtn=str_replace(" Mil Mil", " Un Mil", $Rtn ); 
    while(1) 
    { 
       $Ptr = strpos($Rtn, "Mil ");        
       If(!($Ptr===false)) 
       { 
          If(! (strpos($Rtn, "Mil ",$Ptr + 1) === false )) 
            $this->ReplaceStringFrom($Rtn, "Mil ", "", $Ptr); 
          Else 
           break; 
       } 
       else break; 
    } 

    //--------------------- GoSub FiltroCiento ------------------------------ 
    $Ptr = -1; 
    do{ 
       $Ptr = strpos($Rtn, "Cien ", $Ptr+1); 
       if(!($Ptr===false)) 
       { 
          $Tem = substr($Rtn, $Ptr + 5 ,1); 
          if( $Tem == "M" || $Tem == $this->Void) 
             ; 
          else           
             $this->ReplaceStringFrom($Rtn, "Cien", "Ciento", $Ptr); 
       } 
    }while(!($Ptr === false)); 

    //--------------------- FiltroEspeciales ------------------------------ 
    $Rtn=str_replace("Diez Un", "Once", $Rtn ); 
    $Rtn=str_replace("Diez Dos", "Doce", $Rtn ); 
    $Rtn=str_replace("Diez Tres", "Trece", $Rtn ); 
    $Rtn=str_replace("Diez Cuatro", "Catorce", $Rtn ); 
    $Rtn=str_replace("Diez Cinco", "Quince", $Rtn ); 
    $Rtn=str_replace("Diez Seis", "Dieciseis", $Rtn ); 
    $Rtn=str_replace("Diez Siete", "Diecisiete", $Rtn ); 
    $Rtn=str_replace("Diez Ocho", "Dieciocho", $Rtn ); 
    $Rtn=str_replace("Diez Nueve", "Diecinueve", $Rtn ); 
    $Rtn=str_replace("Veinte Un", "Veintiun", $Rtn ); 
    $Rtn=str_replace("Veinte Dos", "Veintidos", $Rtn ); 
    $Rtn=str_replace("Veinte Tres", "Veintitres", $Rtn ); 
    $Rtn=str_replace("Veinte Cuatro", "Veinticuatro", $Rtn ); 
    $Rtn=str_replace("Veinte Cinco", "Veinticinco", $Rtn ); 
    $Rtn=str_replace("Veinte Seis", "Veintiseís", $Rtn ); 
    $Rtn=str_replace("Veinte Siete", "Veintisiete", $Rtn ); 
    $Rtn=str_replace("Veinte Ocho", "Veintiocho", $Rtn ); 
    $Rtn=str_replace("Veinte Nueve", "Veintinueve", $Rtn ); 

    //--------------------- FiltroUn ------------------------------ 
    If(substr($Rtn,0,1) == "M") $Rtn = "Un " . $Rtn; 
    //--------------------- Adicionar Y ------------------------------ 
    for($i=65; $i<=88; $i++) 
    { 
      If($i != 77) 
         $Rtn=str_replace("a " . Chr($i), "* y " . Chr($i), $Rtn); 
    } 
    $Rtn=str_replace("*", "a" , $Rtn); 
    return($Rtn); 
} 


function ReplaceStringFrom(&$x, $OldWrd, $NewWrd, $Ptr) 
{ 
  $x = substr($x, 0, $Ptr)  . $NewWrd . substr($x, strlen($OldWrd) + $Ptr); 
} 


function Parte($x) 
{ 
    $Rtn=''; 
    $t=''; 
    $i=''; 
    Do 
    { 
      switch($x) 
      { 
         Case 0:  $t = "Cero";break; 
         Case 1:  $t = "Un";break; 
         Case 2:  $t = "Dos";break; 
         Case 3:  $t = "Tres";break; 
         Case 4:  $t = "Cuatro";break; 
         Case 5:  $t = "Cinco";break; 
         Case 6:  $t = "Seis";break; 
         Case 7:  $t = "Siete";break; 
         Case 8:  $t = "Ocho";break; 
         Case 9:  $t = "Nueve";break; 
         Case 10: $t = "Diez";break; 
         Case 20: $t = "Veinte";break; 
         Case 30: $t = "Treinta";break; 
         Case 40: $t = "Cuarenta";break; 
         Case 50: $t = "Cincuenta";break; 
         Case 60: $t = "Sesenta";break; 
         Case 70: $t = "Setenta";break; 
         Case 80: $t = "Ochenta";break; 
         Case 90: $t = "Noventa";break; 
         Case 100: $t = "Cien";break; 
         Case 200: $t = "Doscientos";break; 
         Case 300: $t = "Trescientos";break; 
         Case 400: $t = "Cuatrocientos";break; 
         Case 500: $t = "Quinientos";break; 
         Case 600: $t = "Seiscientos";break; 
         Case 700: $t = "Setecientos";break; 
         Case 800: $t = "Ochocientos";break; 
         Case 900: $t = "Novecientos";break; 
         Case 1000: $t = "Mil";break; 
         Case 1000000: $t = "Millón";break; 
      } 

      If($t == $this->Void) 
      { 
        $i = $i + 1; 
        $x = $x / 1000; 
        If($x== 0) $i = 0; 
      } 
      else 
         break; 
            
    }while($i != 0); 
    
    $Rtn = $t; 
    Switch($i) 
    { 
       Case 0: $t = $this->Void;break; 
       Case 1: $t = " Mil";break; 
       Case 2: $t = " Millones";break; 
       Case 3: $t = " Billones";break; 
    } 
    return($Rtn . $t); 
} 

} 

?>