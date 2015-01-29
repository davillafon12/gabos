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
			break;
			case 'nc':
			break;
			default:
				$this->retorno['error'] = '5';
			break;
		}	
	}
	
}// FIN DE LA CLASE


?>