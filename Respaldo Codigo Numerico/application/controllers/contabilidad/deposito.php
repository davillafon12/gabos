<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class deposito extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('banco','',TRUE);
		$this->load->model('contabilidad','',TRUE);	
	}

	function index()
	{
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if($permisos['deposito_recibos'])
		{
			//this->load->helper(array('form'));
			$bancos = $this->banco->getBancos();
			$data['bancos'] = $bancos;
			$this->load->view('contabilidad/deposito_recibos_view', $data);	
		}
		else{
		   redirect('accesoDenegado', 'location');
		}
	}
	
	function guardarDeposito(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['recibo'])&&isset($_POST['deposito'])&&isset($_POST['banco'])){
			if(trim($_POST['recibo'])!=''&&trim($_POST['deposito'])!=''&&trim($_POST['banco'])!=''){
				include '/../get_session_data.php'; 
				if($reciboResult = $this->contabilidad->existeReciboBySucursal($_POST['recibo'], $data['Sucursal_Codigo'])){
					if($bancoResult = $this->banco->getBanco($_POST['banco'])){
						foreach($reciboResult as $rec){
							$credito = $rec->Credito;
						}
						foreach($bancoResult as $ban){
							$banco_nombre = $ban->Banco_Nombre;
						}
						$this->contabilidad->guardarDepositoRecibo($_POST['recibo'], $credito, $_POST['deposito'], $_POST['banco'], $banco_nombre);
						$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario guardo el deposito: ".$_POST['deposito']." recibo: ".$_POST['recibo'],$data['Sucursal_Codigo'],'anular_recibo');
						$retorno['status'] = 'success';
						unset($retorno['error']);
					}else{
						$retorno['error'] = '5'; //Banco no existe 
					}
				}else{
					$retorno['error'] = '4'; //Recibo no existe 
				}
			}else{
				$retorno['error'] = '3'; //Campo obligatorio vacio
			}			
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}
	
	
}

?>