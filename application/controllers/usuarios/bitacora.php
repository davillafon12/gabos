<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class bitacora extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);
	}

	function index()
	{
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if($permisos['ver_bitacora'])
		{
			//$transacciones = $this->user->getTransacciones();
			//$data['transacciones'] = $transacciones;
			$empresas_actuales = $this->empresa->get_empresas_ids_array();
			$data['Familia_Empresas'] = $empresas_actuales;	
			$this->load->view('usuarios/usuarios_bitacora_view', $data);	
		}
		else{
			redirect('accesoDenegado', 'location');
		}
	}
	
	function obtenerTransaccionesTabla(){
		include PATH_USER_DATA;
		//Un array que contiene el nombre de las columnas que se pueden ordenar
		$columnas = array(
								'0' => 'Trans_Codigo',
								'1' => 'Trans_Descripcion',
								'2' => 'Trans_Fecha_Hora',
								'3' => 'Trans_Tipo',
								'4' => 'Trans_IP',
								'5' => 'Usuario_Nombre_Usuario',
								);
		$query = $this->user->obtenerTransaccionesParaTabla($columnas[$_POST['order'][0]['column']], $_POST['order'][0]['dir'], $_POST['search']['value'], intval($_POST['start']), intval($_POST['length']), $_POST['sucursal']);
		
		$transaccionesAMostrar = array();
		foreach($query->result() as $trans){
			$auxArray = array(
				$trans->codigo,
				$trans->descripcion,
				$trans->fecha,
				$trans->tipo,
				$trans->ip,
				$trans->usuario_codigo." - ".$trans->usuario_user." / ".$trans->usuario_nombre
			);
			array_push($transaccionesAMostrar, $auxArray);
		}
		
		$filtrados = $this->user->obtenerTransaccionesParaTablaFiltrados($columnas[$_POST['order'][0]['column']], $_POST['order'][0]['dir'], $_POST['search']['value'], intval($_POST['start']), intval($_POST['length']), $_POST['sucursal']);
		
		$retorno = array(
					'draw' => $_POST['draw'],
					'recordsTotal' => $this->user->getTotalTransaccionesEnSucursal($_POST['sucursal']),
					'recordsFiltered' => $filtrados -> num_rows(),
					'data' => $transaccionesAMostrar
				);
		echo json_encode($retorno);
	}

}

?>