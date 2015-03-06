<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');

class impresion extends CI_Controller {

	private $tokenSeguridad = '';
	private $retorno = array();
	private $numPagina = 0;
	
	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);
		$this->load->model('factura','',TRUE);
		$this->load->model('contabilidad','',TRUE);
		$this->load->model('proforma_m','',TRUE);
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
								$facturaHead[0] -> cantidadTarjeta = $cantidadPagaTarjeta;
								$facturaHead[0] -> cantidadContado = $cantidadPagaContado;
							}elseif($facturaHead[0] -> tipo == 'apartado'){								
								$abono = $this->factura->getAbonoApartado($sucursal, $consecutivo);
								$facturaHead[0] -> abono = $abono;
							}
						
							if($facturaBody = $this->factura->getArticulosFacturaImpresion($consecutivo, $sucursal)){
								//var_dump($empresa);
								
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
								$facturaHead[0] -> cantidadTarjeta = $cantidadPagaTarjeta;
								$facturaHead[0] -> cantidadContado = $cantidadPagaContado;
							}elseif($facturaHead[0] -> tipo == 'apartado'){								
								$abono = $this->factura->getAbonoApartado($sucursal, $consecutivo);
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
			
			$this->printProducts($fbody, $inicio, $final-1, $pdf);
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
			
			$this->printProducts($fbody, $inicio, $final-1, $pdf);
			//Definimos el pie de pagina
			$this->pieDocumentoPDF('p', $fhead[0], $empresa[0], $pdf);
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
	
	private function printProducts($productos, $inicio, $fin, &$pdf){
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
			
			$pdf->Text(11, $pl, $productos[$cc]->codigo);
			$pdf->Text(31, $pl, substr($productos[$cc]->descripcion,0,33));
			$pdf->cell(15,5,$productos[$cc]->cantidad,0,0,'C');
			$pdf->cell(6,5,$this->fe($productos[$cc]->exento),0,0,'C');
			$pdf->cell(14,5,$this->fn($productos[$cc]->descuento),0,0,'C');
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