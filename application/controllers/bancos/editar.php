<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class editar extends CI_Controller {

 function __construct()
 {
    parent::__construct(); 
	$this->load->model('user','',TRUE);
	$this->load->model('banco','',TRUE);
	$this->load->model('user','',TRUE);
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if(!$permisos['entrar_banco'])
	{	
	    redirect('accesoDenegado', 'location');	
	}
 }

 function index()
 {
	
	 include PATH_USER_DATA;
	 $this->load->view('bancos/bancos_edicion_view', $data);	
 }
 
 function getMainTable()
 {	
	$ruta_imagen = base_url('application/images/Icons');
	
	echo "<table id='tabla_editar' class='tablaPrincipal'>";
	echo "<thead> <tr>
					<th >
						
					</th>
					<th class='Sorted_enabled'>
						Código
					</th>
					<th class='Sorted_enabled'>
						Nombre
					</th>
					<th class='Sorted_enabled'>
						Porcentaje De Comisión
					</th>
					<th class='Sorted_enabled'>
						Creado Por
					</th>
					<th >
						Opciones
					</th>
				</tr></thead><tbody>";
				
	if($result = $this->banco->getBancos())
	{		
		foreach($result as $row)
		{
			echo "<tr class='table_row'>
					<td >
						<input class='checkbox'  type='checkbox' name='checkbox' value='".$row->Banco_Codigo."'>
					</td>
					<td>
						".$row->Banco_Codigo."
					</td>
					<td >
						".$row->Banco_Nombre."
					</td>
					<td>
						".$row->Banco_Comision_Porcentaje."
					</td>
					<td>
						".$this->user->get_name($row->Banco_Creado_Por)."
					</td>
					<td >
						<div class='tab_opciones'>
							<a href='".base_url('')."bancos/editar/edicion?id=".$row->Banco_Codigo."' ><img src=".$ruta_imagen."/editar.png width='21' height='21' title='Editar'></a>
							<a href='javascript:;' onclick='goEliminar(".$row->Banco_Codigo.")'><img src=".$ruta_imagen."/eliminar.png width='17' height='17' title='Eliminar'></a>
							
						</div>
					</td>
				</tr>";
		}			
	}
	echo "</tbody></table>";
	echo "<div class='div_bot_des'>
				<a href='javascript:;' onClick='resetCheckBox()' class='boton_desall'>Deseleccionar Todo</a>
				<a href='javascript:;' onClick='desAllChecked()' class='boton_des_all' >Eliminar</a>
				<a href='".base_url('')."bancos/registrar' class='boton_agregar'>Agregar Banco</a>
		  </div>";	
 }//FIN DE GETTABLE
 
	function eliminar()
	 {
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		
		if(!$permisos['desactivar_banco'])
		{	
			redirect('accesoDenegado', 'location');	
		}
		
		$bancos=$_GET['array'];
		$bancos=explode(',', $bancos);
		foreach($bancos as $banco_id)
		{
			$this->banco->eliminar($banco_id);
			$this->user->guardar_transaccion($data['Usuario_Codigo'], "Elimino el banco codigo: ".$banco_id,$data['Sucursal_Codigo'],'eliminar');
		}
	 }
	 
	 
	function edicion()
	 {
		$id_request=$_GET['id'];
		
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		
		if(!$permisos['editar_banco'])
		{	
			redirect('accesoDenegado', 'location');	
		}
		
		if($result = $this->banco->getBanco($id_request))
		{
			$this->load->helper(array('form'));
			foreach($result as $row)
			{
				$data['Banco_Codigo'] = $id_request;
				$data['Banco_Nombre'] = $row -> Banco_Nombre;
				$data['Banco_Comision_Porcentaje'] = $row -> Banco_Comision_Porcentaje;
				//echo $data['Empresa_Observaciones'];
			}
			$this->load->view('bancos/bancos_editar_view', $data);
		}
		else
		{}
	 }
	 
	 function actualizar()
	 {
		$id_banco = $this->input->post('codigo');
		$nombre_banco = $this->input->post('name');
		$comision_banco = $this->input->post('comision');
			
		$data_update['Banco_Nombre'] = mysql_real_escape_string($nombre_banco);
		$data_update['Banco_Comision_Porcentaje'] = mysql_real_escape_string($comision_banco);
		
		//echo $id_empresa;
		//echo $nombre_empresa;
		
		$this->banco->actualizar(mysql_real_escape_string($id_banco), $data_update);
		
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario editó el banco codigo: ".mysql_real_escape_string($id_banco),$data['Sucursal_Codigo'],'edicion');
		
		redirect('bancos/editar', 'location');
	 }
 
 
 
	
 }// FIN DE LA CLASE


?>