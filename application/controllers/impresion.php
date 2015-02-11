<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');

class impresion extends CI_Controller {

	private $tokenSeguridad = '';
	private $retorno = array();
	
	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);
		$this->load->model('factura','',TRUE);
		$this->load->model('contabilidad','',TRUE);
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
	
}// FIN DE LA CLASE


?>