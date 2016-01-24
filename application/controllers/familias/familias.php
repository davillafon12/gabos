<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class familias extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('familia','',TRUE);
		$this->load->model('empresa','',TRUE);
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['entrada_familias'])
		{	
		   redirect('accesoDenegado', 'location');
		}
	}

	function index()
	{
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		
		$this->load->view('familias/view_home_familias', $data);	
	}
	
	function getMainTable()
	 {	
		include '/../get_session_data.php';
		$ruta_imagen = base_url('application/images/Icons');
		
		if($result = $this->familia->getFamiliasTodas())
		{
			/*
							<th class='Sorted_enabled'>
								Descuento
							</th>*/
			//echo "entro a getmaintable";
			//echo "<div class='busqueda'><label for='input-filter'>Filtrar:</label><input type='search' id='input-filter' size='15' ></div>";
			//echo "<div class='tablaP2rincipal'>";		
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
							<th >
								Observaciones
							</th>
							<th class='Sorted_enabled'>
								Estado
							</th>							
							<th class='Sorted_enabled'>
								Creado Por
							</th>
							<th class='Sorted_enabled'>
								Empresa
							</th>
							<th >
								Opciones
							</th>
						</tr></thead> <tbody>";
				foreach($result as $row)
				{
					$nombre_empresa = $this->empresa->getNombreEmpresa($row->TB_02_Sucursal_Codigo);
					echo "<tr class='table_row'>
							<td >
								<input class='checkbox'  type='checkbox' name='checkbox' value='".$row->Familia_Codigo."_".$row->TB_02_Sucursal_Codigo."'>
							</td>
							<td>
								".$row->Familia_Codigo."
							</td>
							<td >
								".$row->Familia_Nombre."
							</td>
							
							<td class='Tab_Observaciones'>
								".$row->Familia_Observaciones."
							</td>
							<td class='tab_fecha'>";
							If($row->Familia_Estado)
							{
								echo "<div class='estado_Ac'>ACTIVADO</div><br>
									  Ingreso = ".$row->Familia_Fecha_Creacion; 
							}
							else
							{
								echo "<div class='estado_De'>DESACTIVADO</div><br>
									  Ingreso = ".$row->Familia_Fecha_Creacion."<br>
									  Salida = ".$row->Familia_Fecha_Desactivacion;
							}
							echo "</td >
							<td>
								".$row->Familia_Creador."
							</td>
							<td >
								".$row->TB_02_Sucursal_Codigo." - ".$nombre_empresa."
							</td>
							<td >
								<div class='tab_opciones'>
									<a href='".base_url('')."familias/familias/edicion?id=".$row->Familia_Codigo."&sucursal=".$row->TB_02_Sucursal_Codigo."' ><img src=".$ruta_imagen."/editar.png width='21' height='21' title='Editar'></a>
									<a href='javascript:;' onclick='goDesactivar(\"".$row->Familia_Codigo."_".$row->TB_02_Sucursal_Codigo."\")' '><img src=".$ruta_imagen."/eliminar.png width='17' height='17' title='Desactivar'></a>
									<a href='javascript:;' onclick='goActivar(\"".$row->Familia_Codigo."_".$row->TB_02_Sucursal_Codigo."\")' '><img src=".$ruta_imagen."/activar.png width='21' height='21' title='Activar'></a>
								</div>
							</td>
						</tr>";
				}
			echo "</tbody></table>";
			//echo "</div>";
			echo "<div class='div_bot_des'>
						<a href='javascript:;' onClick='resetCheckBox()' class='boton_desall'>Deseleccionar Todo</a>
						<a href='javascript:;' onClick='desAllChecked()' class='boton_des_all' >Desactivar</a>
						<a href='javascript:;' onClick='actAllChecked()' class='boton_act_all' >Activar</a>
						<a href='".base_url('')."familias/registrar' class='boton_agregar'>Agregar Familia</a>
				  </div>";
		}
		else
		{
			/*
							<th class='Sorted_enabled'>
								Descuento
							</th>*/
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
							<th >
								Observaciones
							</th>
							<th class='Sorted_enabled'>
								Fecha De Creación
							</th>
							<th class='Sorted_enabled'>
								Fecha De Desactivación
							</th >
							<th class='Sorted_enabled'>
								Creado Por
							</th>
							<th class='Sorted_enabled'>
								Empresa
							</th>
							<th >
								Opciones
							</th>
						</tr></thead><tbody></tbody> </table>";
						echo "<div class='div_bot_des'>
						<a href='".base_url('')."familias/registrar' class='boton_agregar'>Agregar Familia</a>
						
				        </div>";
		}
	 }//FIN DE GETTABLE
 
	function desactivar()
	 {
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['desactivar_familias'])
		{	
		   redirect('accesoDenegado', 'location');
		}
		//echo "PASO AQUI";
		
		date_default_timezone_set("America/Costa_Rica");
		$Current_datetime = date("y/m/d : H:i:s", now());
		$familias=$_GET['array'];
		$familias=explode(',', $familias);
		$data_update['Familia_Fecha_Desactivacion'] = $Current_datetime;
		$data_update['Familia_Estado'] = 0;
		foreach($familias as $familia_id)
		{
			$familia_array = explode("_", $familia_id);
			$familia_id = $familia_array[0];
			$familia_sucursal = $familia_array[1];
			if($this->familia->isActivated($familia_id, $familia_sucursal))
			{ 
				$this->familia->actualizar($familia_id, $familia_sucursal, $data_update);
				
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario desactivo a la familia codigo: ".$familia_id." de la sucursal: ".$familia_sucursal,$data['Sucursal_Codigo'],'edicion');
			}
		}
	 }
 
	function activar()
	{
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['activar_familias'])
		{	
		   redirect('accesoDenegado', 'location');
		}
		//echo "PASO AQUI";
		date_default_timezone_set("America/Costa_Rica");
		$Current_datetime = date("y/m/d : H:i:s", now());
		$familias=$_GET['array'];
		$familias=explode(',', $familias);
		$data_update['Familia_Fecha_Creacion'] = $Current_datetime;
		$data_update['Familia_Fecha_Desactivacion'] = NULL;
		$data_update['Familia_Estado'] = 1;
		foreach($familias as $familia_id)
		{
			$familia_array = explode("_", $familia_id);
			$familia_id = $familia_array[0];
			$familia_sucursal = $familia_array[1];
			if(!$this->familia->isActivated($familia_id, $familia_sucursal))
			{
				$this->familia->actualizar($familia_id, $familia_sucursal, $data_update);
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario activo a la empresa codigo: ".$familia_id." de la sucursal ".$familia_sucursal,$data['Sucursal_Codigo'],'edicion');
			}
		}
	}
	
	function edicion()
	{
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$id_request = $_GET['id'];
		$sucursal = $_GET['sucursal'];
		$ruta_base_imagenes_script = base_url('application/images/scripts');
		$nombre_familia = $this->familia->getNombreFamiliaSucursal($id_request, $sucursal);
		
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['editar_familias'])
		{	
		   redirect('accesoDenegado', 'location');
		}
		
		//echo $nombre_empresa;
		if($result = $this->familia->getFamilia($id_request, $sucursal))
		{
			$this->load->helper(array('form'));
			foreach($result as $row)
			{
			    $empresas_actuales = $this->empresa->get_empresas_ids_array();
				$data['Familia_codigo'] = $id_request;
				$data['Familia_nombre'] = $row -> Familia_Nombre;
				//$data['Familia_Descuento'] = $row -> Familia_Descuento;				
				$data['Familia_Observaciones'] = $row -> Familia_Observaciones;
				$data['Familia_Sucursal'] = $row -> TB_02_Sucursal_Codigo;
				$data['Familia_Empresas'] = $empresas_actuales;
				//echo $data['Empresa_Observaciones'];
			}
			$this->load->view('familias/edicion_view_familias', $data);
		}
		else
		{
			$data['Titulo_Pagina'] = "Transacción Fallida";
			$data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>Hubo un error al actualizar la familia ".$nombre_familia."! <img src=".$ruta_base_imagenes_script."/error.gif /></p></div><br>
									 <div class='Informacion'>								 
									 <form action=".base_url('familias/familias').">
										 <input class='buttom' tabindex='2' value='Volver' type='submit' >
									 </form>								 
									 </div>";
			$this->load->view('empresas/view_informacion_guardado', $data);
		}
	}
	
	 function actualizarFamilia()
	{
		$id_familia = $this->input->post('codigo');
		$nombre_familia = $this->input->post('name');
		//$descuento_familia = $this->input->post('descuento');
		$observaciones_familia = $this->input->post('observaciones');
		$sucursal_familia = $this->input->post('sucursal');
		
			
		$data_update['Familia_Nombre'] = mysql_real_escape_string($nombre_familia);
		//$data_update['Familia_Descuento'] = mysql_real_escape_string($descuento_familia);
		//$data_update['Familia_Direccion'] = mysql_real_escape_string($direccion_empresa);
		$data_update['Familia_Observaciones'] = mysql_real_escape_string($observaciones_familia);
        //$data_update['TB_02_Sucursal_Codigo'] = mysql_real_escape_string($sucursal_familia);
		//echo $id_empresa;
		//echo $nombre_empresa;

		$this->familia->actualizar(mysql_real_escape_string($id_familia), $sucursal_familia, $data_update);

		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario editó la familia codigo: ".mysql_real_escape_string($id_familia),$data['Sucursal_Codigo'],'edicion');

		redirect('familias/familias', 'location');
	}
 



}

?>