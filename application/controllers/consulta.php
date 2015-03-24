<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class consulta extends CI_Controller {	
	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		//$this->load->model('empresa','',TRUE);
		$this->load->model('factura','',TRUE);
		$this->load->model('contabilidad','',TRUE);
		//$this->load->model('proforma_m','',TRUE);
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
					$this->retorno['error'] = '11';
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
				if($notaCreditoBody = $this->contabilidad->getArticulosNotaCreditoParaImpresion($consecutivo, $data['Sucursal_Codigo'])){
					unset($retorno['error']);
					$retorno['status'] = 'success';
					$retorno['notaHead'] = $notaCreditoHead;
					$retorno['notaBody'] = $notaCreditoBody;
					
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

} 

?>