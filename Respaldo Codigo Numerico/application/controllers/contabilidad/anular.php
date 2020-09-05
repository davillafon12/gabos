<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class anular extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('cliente','',TRUE);
		$this->load->model('contabilidad','',TRUE);	
	}

	function index()
	{
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if($permisos['anular_recibos'])
		{
			//this->load->helper(array('form'));
			$this->load->view('contabilidad/anular_recibos_view', $data);	
		}
		else{
		   redirect('accesoDenegado', 'location');
		}
	}
	
	function getRecibos(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['cedula'])){
			$cedula = $_POST['cedula']; 
			if(trim($cedula) == '1' || trim($cedula) == '0'){
				$retorno['error'] = '4'; //Error cliente contado y afiliado
			}else{
				if($clienteArray = $this->cliente->getClientes_Cedula($cedula)){
					include '/../get_session_data.php';					
					if($recibos = $this -> contabilidad -> getRecibos($cedula, $data['Sucursal_Codigo'])){
						$rec_arr = array();
						foreach($recibos as $rec){
							//Credito, Recibo_Cantidad, Recibo_Fecha, Recibo_Saldo, Credito_Factura_Consecutivo
							array_push($rec_arr, array(
														'consecutivo' => $rec->Consecutivo,
														'credito' => $rec->Credito,
														'cantidad' => $rec->Recibo_Cantidad,
														'fecha' => $rec->Recibo_Fecha,
														'saldo' => $rec->Recibo_Saldo,
														'factura' => $rec->Credito_Factura_Consecutivo
														));
						}
						foreach($clienteArray as $row){
							$cliente['nombre'] = $row-> Cliente_Nombre;
							$cliente['apellidos'] = $row-> Cliente_Apellidos;						
						}
						unset($retorno['error']);
						$retorno['status'] = 'success';
						$retorno['recibos'] = $rec_arr;
						$retorno['cliente'] = $cliente;					
					}else{
						$retorno['error'] = '5'; //Error no hay recibos para este cliente
					}					
				}else{
					$retorno['error'] = '3'; //Error no hay cliente
				}
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}
	
	function anularRecibo(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['recibo'])&&isset($_POST['credito'])){
			if($this->contabilidad->existeRecibo($_POST['recibo'], $_POST['credito'])){
				$cantidadRecibo = $this->contabilidad->getMontoRecibo($_POST['recibo'], $_POST['credito']);
				$cantidadCredito = $this->contabilidad->getMontoCredito($_POST['credito']);
				$cantidadFinal = $cantidadRecibo+$cantidadCredito;
				
				$datos = array('Credito_Saldo_Actual' => $cantidadFinal);
				//Devolvemos el dinero
				$this->contabilidad->actualizarCredito($datos, $_POST['credito']);
				//Indicamos que el recibo fue anulado
				$this->contabilidad->flagAnularRecibo($_POST['recibo'], $_POST['credito']);
				
				include '/../get_session_data.php';	
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario anuló el recibo ".$_POST['recibo']." credito: ".$_POST['credito'],$data['Sucursal_Codigo'],'anular_recibo');
				$retorno['status'] = 'success';
				unset($retorno['error']);
			}else{
				$retorno['error'] = '6'; //No existe recibo
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}
}

?>