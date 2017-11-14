<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class editar extends CI_Controller {


	private $flag; 
	private $nombreImagenTemp; 
 function __construct()
 {
    parent::__construct(); 
	$this->load->model('user','',TRUE);
	$this->load->model('empresa','',TRUE);
 }

 function index()
 {
	include FCPATH.'application/controllers/get_session_data.php'; //Esto es para traer la informacion de la sesion
		
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	if($permisos['editar_usuarios'])
	{	
	    $this->load->view('usuarios/usuarios_editar_view', $data);	
	}
	else{
	   redirect('accesoDenegado', 'location');
	}	
 }

 function mostrar_todos_los_datos(){
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if($permisos['editar_usuarios'])
	{	
	    $this->load->view('usuarios/usuarios_editar2_view', $data);	
	}
	else{
	   redirect('accesoDenegado', 'location');
	}	

 }

  function getMainTable()
 {	
	$ruta_imagen = base_url('application/images/Icons');
	if($result = $this->user->getUsuarios())
	{
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
                        <th class='Sorted_enabled'>
                            Apellidos
                        </th>
                        <th class='Sorted_enabled'>
                            Nombre Usuario
                        </th>
						<th class='Sorted_enabled'>
                            Cédula
                        </th>
						<th class='Sorted_enabled estado-centrado'>
                            Estado
                        </th>						
						<th >
                            Opciones
                        </th>
                    </tr></thead> <tbody>";
			foreach($result as $row)
			{
					echo "<tr class='table_row'>

						<td >
                            <input class='checkbox'  type='checkbox' name='checkbox' value='".$row->Usuario_Codigo."'>
                        </td>
						<td>
							".$row->Usuario_Codigo."
						</td>
                        <td>
                            ".$row->Usuario_Nombre."
                        </td>
                        <td >
                            ".$row->Usuario_Apellidos."
                        </td>
                        <td>
                            ".$row->Usuario_Nombre_Usuario."
                        </td>
						<td>
                            ".$row->Usuario_Cedula."
                        </td>
						<td>";
						if($this->user->isActivated($row->Usuario_Codigo)){
							//Esta activado
							echo "<div class='estado_Ac'>ACTIVADO</div><br>"; 
						}else{
							//Esta desactivado
							echo "<div class='estado_De'>DESACTIVADO</div><br>"; 
						}
				echo	"</td>
						<td >
							<div class='tab_opciones'>
								<a href='".base_url('')."usuarios/editar/edicion?id=".$row->Usuario_Codigo."' ><img src=".$ruta_imagen."/editar.png width='21' height='21' title='Editar'></a>
								<a href='javascript:;' onclick='goDesactivar(".$row->Usuario_Codigo.")'><img src=".$ruta_imagen."/eliminar.png width='17' height='17' title='Desactivar'></a>
								<a href='javascript:;' onclick='goActivar(".$row->Usuario_Codigo.")'><img src=".$ruta_imagen."/activar.png width='21' height='21' title='Activar'></a>
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
					<a href='".base_url('')."usuarios/registrar' class='boton_agregar'>Agregar</a>					
			  </div>";
			  
	}
 }//FIN DE GETTABLE

 






 function desactivar()
 {
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
	date_default_timezone_set("America/Costa_Rica");
	$fecha_desactivacion = date("y/m/d : H:i:s", now());
	$fecha_recontratacion = NULL; 
	$data_update['Usuario_Fecha_Cesantia'] = $fecha_desactivacion;
	$data_update['Usuario_Fecha_Recontratacion'] = $fecha_recontratacion;
	$user_a_activar=$_GET['array'];
	$user_a_activar=explode(',', $user_a_activar);
	foreach($user_a_activar as $cliente_id)
	{
		if($this->user->isActivated($cliente_id))
		{
			$this->user->actualizar($cliente_id, $data_update);
			$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario activo al usuario código: ".$cliente_id,$data['Sucursal_Codigo'],'edicion');
		}
	}
 }
 
 function activar()
 {
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		date_default_timezone_set("America/Costa_Rica");
		$fecha_desactivacion = NULL; 
		$fecha_recontratacion = date("y/m/d : H:i:s", now());
		$data_update['Usuario_Fecha_Cesantia'] = $fecha_desactivacion;
		$data_update['Usuario_Fecha_Recontratacion'] = $fecha_recontratacion;
		$user_a_activar=$_GET['array'];
		$user_a_activar=explode(',', $user_a_activar);
		foreach($user_a_activar as $cliente_id)
		{
			if(!$this->user->isActivated($cliente_id))
			{
				$this->user->actualizar($cliente_id, $data_update);
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario activo al usuario código: ".$cliente_id,$data['Sucursal_Codigo'],'edicion');
			}
		}
 }

 function edicion()
 {
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	if(isset($_GET['id'])){
		$id_request=$_GET['id'];
		
		$ruta_imagen_usuario = base_url('application/images/User_Photos/thumb');
		include FCPATH.'application/controllers/get_session_data.php'; //Esto es para traer la informacion de la sesion
		
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		
		$data['permisosUserLogin'] = $permisos;
		
		if(!$permisos['editar_usuarios'])
		{	
			redirect('accesoDenegado', 'location');	
		}
		if($result = $this->user->getUsuario_Codigo($id_request))
		{
			$this->load->helper(array('form'));
			foreach($result as $row)
			{
				$data['Usuario_Codigo_Modificar'] = $row -> Usuario_Codigo;
				$data['Nombre_Usuario'] = $row -> Usuario_Nombre;
				$data['Apellidos_Usuario'] = $row -> Usuario_Apellidos;
				$data['Usuario_Cedula'] = $row -> Usuario_Cedula;
				$data['Usuario_Tipo_Cedula'] = $row -> Usuario_Tipo_Cedula;
				$data['Usuario_Celular'] = $row -> Usuario_Celular;
				$data['Usuario_Telefono'] = $row -> Usuario_Telefono;
				$formato_Fecha_Ing = substr($row -> Usuario_Fecha_Ingreso, -20, 10);
				$data['Usuario_Fecha_Ingreso'] = $formato_Fecha_Ing;
				$formato_Fecha_Ces = substr($row -> Usuario_Fecha_Cesantia, -20, 10);
				$data['Usuario_Fecha_Cesantia'] = $formato_Fecha_Ces;
				$formato_Fecha_Rec = substr($row -> Usuario_Fecha_Recontratacion, -20, 10);
				$data['Usuario_Fecha_Recontratacion'] = $formato_Fecha_Rec;
				$data['Usuario_Nombre_User'] = $row -> Usuario_Nombre_Usuario;
				$data['Observaciones_Usuario'] = $row -> Usuario_Observaciones;
				$data['Imagen_Usuario'] = $ruta_imagen_usuario."/".$row -> Usuario_Imagen_URL;
				$data['Usuario_Correo_Electronico'] = $row -> Usuario_Correo_Electronico;
				$data['Usuario_Nivel'] = $row -> Usuario_Rango;	
				$empresas_actuales = $this->empresa->get_empresas_ids_array();
				$data['Familia_Empresas'] = $empresas_actuales;
				$data['Sucursal_Codigo'] = $row -> TB_02_Sucursal_Codigo; 
				$data['permisos_usuario'] = $this->user->get_permisos($row -> Usuario_Codigo, $row -> TB_02_Sucursal_Codigo);	
				//$data['permisos_usuario'] = $permisos;
			}
			//echo 'formato fecha  = '.$data['Usuario_Fecha_Ingreso'];
			//if(!$data['Usuario_Codigo']==$data['Usuario_Codigo_Modificar'] ){
				$this->load->view('usuarios/usuarios_edision_view', $data);
			/*}
			else{
				$this->load->view('usuarios/usuarios_editar_view', $data);	
			}*/
		}
		else
		{
			$data['Titulo_Pagina'] = "Transacción Fallida";
			$data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>Hubo un error al actualizar el usuario ".$id_request."! <img src=".$ruta_base_imagenes_script."/error.gif /></p></div><br>
									 <div class='Informacion'>								 
									 <form action=".base_url('usuarios/editar').">
										 <input class='buttom' tabindex='2' value='Volver' type='submit' >
									 </form>								 
									 </div>";
		}
	}else{
		redirect('usuarios/editar', 'location');	
	}
 }
 
 function actualizarUsuario()
 {
	if(isset($_POST['codigo_usuario'])){ //Si viene el codigo del usuario entra
		if(isset($_POST['sucursal_usuario_original'])){ //Si viene el codigo de la sucursal
			$codigo_usuario = $_POST['codigo_usuario'];
			$sucursal_usuario = $_POST['sucursal_usuario_original'];
			if($this->user->existeUsuario($codigo_usuario, $sucursal_usuario)){ //Si existe el usuario
				//print_r($_POST);
				date_default_timezone_set("America/Costa_Rica");
				$Current_datetime = date("y/m/d : H:i:s", now());
				
				$nombre_usuario = $this->input->post('nombre_usuario');
				$apellidos_usuario = $this->input->post('apellidos_usuario');
				$cedula_usuario = $this->input->post('cedula_usuario');
				$tipo_cedula_usuario = $this->input->post('tipo_cedula');
				$celular_usuario = $this->input->post('celular_usuario');
				$telefono_usuario = $this->input->post('telefono_usuario');
				//$fecha_ingreso_usuario = $this->input->post('fecha_ingreso');
				$fecha_cesantia_usuario = $this->input->post('fecha_cesantia');
				$fecha_recontratacion_Usuario = $this->input->post('fecha_recontratacion');
				$usuario_nombre_usuario = $this->input->post('usuario_nombre_usuario');
				$observaciones = $this->input->post('observaciones');
				$usuario_password = $this->input->post('usuario_password');
				$email_usuario = $this->input->post('email_usuario');
				$rango_usuario = $this->input->post('usuario_rango');
				$nombre_usuario_original = $this->input->post('nombre_usuario_original');
				

				if($result = $this->user->obtener_Imagen_Usuario($cedula_usuario)){
					foreach($result as $row)
					{
						$temp = $row -> Usuario_Imagen_URL;
						if(!$temp == "Default.png"){
							$this->flag = true; 
							$this->nombreImagenTemp = $temp; 
						}
						else{
							$this->flag = false; 
							$this->nombreImagenTemp = $temp; 
						}
					}
				}

				$this->do_upload($cedula_usuario."_".$sucursal_usuario); // metodo encargado de cargar la imagen con la cedula del usuario
				
				$data_update['Usuario_Cedula'] = mysql_real_escape_string($cedula_usuario);
				//$data_update['TB_02_Sucursal_Codigo'] =  mysql_real_escape_string($sucursal_usuario);
				$data_update['Usuario_Nombre'] = mysql_real_escape_string($nombre_usuario);
				$data_update['Usuario_Apellidos'] = mysql_real_escape_string($apellidos_usuario);
				$data_update['Usuario_Celular'] = mysql_real_escape_string($celular_usuario);
				$data_update['Usuario_Telefono'] = mysql_real_escape_string($telefono_usuario);
				//$data_update['Usuario_Fecha_Ingreso'] = mysql_real_escape_string($fecha_ingreso_usuario);
				if (!empty($fecha_cesantia_usuario)) {
					$data_update['Usuario_Fecha_Cesantia'] = mysql_real_escape_string($fecha_cesantia_usuario);
				}
				if (!empty($fecha_recontratacion_Usuario)) {
					$data_update['Usuario_Fecha_Recontratacion'] = mysql_real_escape_string($fecha_recontratacion_Usuario);
				}
				
				if($nombre_usuario_original!=$usuario_nombre_usuario){				
					$data_update['Usuario_Nombre_Usuario'] = mysql_real_escape_string($usuario_nombre_usuario);
				}
				$data_update['Usuario_Observaciones'] = mysql_real_escape_string($observaciones);
				$data_update['Usuario_Imagen_URL'] = mysql_real_escape_string($this->direccion_url_imagen);
				$data_update['Usuario_Correo_Electronico'] = mysql_real_escape_string($email_usuario);
				$data_update['Usuario_Rango'] = mysql_real_escape_string($rango_usuario);
				
				if($usuario_password){
					$data_update['Usuario_Password'] = MD5($usuario_password);     
				}
				$this->user->actualizar($codigo_usuario, $data_update);
				
				//Edicion de permisos
				include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
				$permisosArray = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
				if(isset($permisosArray['editar_permisos'])&&$permisosArray['editar_permisos']){
						//Eliminar todos los permisos
						$this->user->eliminarPermisosUsuario($codigo_usuario, $sucursal_usuario);
						
						//Agregamos nuevos permisos
						if(isset($_POST['permisos'])){
							foreach($_POST['permisos'] as $permiso){			
								$this->user->agregarPermiso($codigo_usuario, $sucursal_usuario, $permiso, 1);
							}			
						}	
				}		
				
				
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario editó el usuario codigo: ".mysql_real_escape_string($codigo_usuario),$data['Sucursal_Codigo'],'edicion');
				//echo "tipo cedula ".$tipo_cedula_usuario;
			}
		}
	}
	redirect('usuarios/editar', 'location');
 }

 function verMiPerfil(){
	//$id_request=$_GET['id'];
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	$ruta_imagen_usuario = base_url('application/images/User_Photos/thumb');
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
	if($result = $this->user->getUsuario_Codigo($data['Usuario_Codigo']))
	{
	    $this->load->helper(array('form'));
	    foreach($result as $row)
		{
			$data['Usuario_Codigo_Modificar'] = $row -> Usuario_Codigo;
			$data['Nombre_Usuario'] = $row -> Usuario_Nombre;
			$data['Apellidos_Usuario'] = $row -> Usuario_Apellidos;
			$data['Usuario_Cedula'] = $row -> Usuario_Cedula;
			$data['Usuario_Tipo_Cedula'] = $row -> Usuario_Tipo_Cedula;
			$data['Usuario_Celular'] = $row -> Usuario_Celular;
			$data['Usuario_Telefono'] = $row -> Usuario_Telefono;
			$formato_Fecha_Ing = substr($row -> Usuario_Fecha_Ingreso, -20, 10);
			$data['Usuario_Fecha_Ingreso'] = $formato_Fecha_Ing;
			$formato_Fecha_Ces = substr($row -> Usuario_Fecha_Cesantia, -20, 10);
			$data['Usuario_Fecha_Cesantia'] = $formato_Fecha_Ces;
			$formato_Fecha_Rec = substr($row -> Usuario_Fecha_Recontratacion, -20, 10);
			$data['Usuario_Fecha_Recontratacion'] = $formato_Fecha_Rec;
			$data['Usuario_Nombre_User'] = $row -> Usuario_Nombre_Usuario;
			$data['Observaciones_Usuario'] = $row -> Usuario_Observaciones;
			$data['Imagen_Usuario'] = $ruta_imagen_usuario."/".$row -> Usuario_Imagen_URL;
			$data['Usuario_Correo_Electronico'] = $row -> Usuario_Correo_Electronico;
			$data['Usuario_Nivel'] = $row -> Usuario_Rango;		
			$data['Sucursal_Nombre'] = $this->empresa->getNombreEmpresa( $row -> TB_02_Sucursal_Codigo); 
				
		}
			$this->load->view('usuarios/usuarios_perfil_view', $data);

	}
	else
	{
		$data['Titulo_Pagina'] = "Transacción Fallida";
		$data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>Hubo un error al mostrar Perfil del usuario ".$id_request."! <img src=".$ruta_base_imagenes_script."/error.gif /></p></div><br>
		                         <div class='Informacion'>								 
					             <form action=".base_url('home').">
									 <input class='buttom' tabindex='2' value='Volver' type='submit' >
				                 </form>								 
								 </div>";
	}

 }

 function actualizarMiPerfil(){
	/*$usuario_password = $this->input->post('usuario_password');
	$cedula_usuario = $this->input->post('cedula_usuario');

	$data_update['Usuario_Password'] =MD5($usuario_password);  
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
	$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario editó el usuario codigo: ".mysql_real_escape_string($cedula_usuario),$data['Sucursal_Codigo'],'edicion');
	$this->user->actualizar(mysql_real_escape_string($cedula_usuario), $data_update);
	
	redirect('home', 'location');*/
	
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		
	$nombre_usuario = $this->input->post('nombre_usuario');
	$apellidos_usuario = $this->input->post('apellidos_usuario');
	$cedula_usuario = $this->input->post('cedula_usuario');
	$tipo_cedula_usuario = $this->input->post('tipo_cedula');
	$celular_usuario = $this->input->post('celular_usuario');
	$telefono_usuario = $this->input->post('telefono_usuario');	
	$email_usuario = $this->input->post('email_usuario');
	$usuario_password = $this->input->post('usuario_password');
	$usuario_password_actual = $this->input->post('usuario_password_actual');
	$sucursal_usuario = $data['Sucursal_Codigo'];
	$codigo_usuario = $data['Usuario_Codigo'];
	
	//echo $cedula_usuario;
	//print_r($_POST);
	
	if($result = $this->user->obtener_Imagen_Usuario($cedula_usuario)){
		foreach($result as $row)
		{
			$temp = $row -> Usuario_Imagen_URL;
			if(!$temp == "Default.png"){
				$this->flag = true; 
				$this->nombreImagenTemp = $temp; 
			}
			else{
				$this->flag = false; 
				$this->nombreImagenTemp = $temp; 
			}
		}
	}
	
	//if($_FILES['userfile']['name']!=''){ //El nombre del archivo no sea vacio
		$this->do_upload($cedula_usuario."_".$sucursal_usuario); // metodo encargado de cargar la imagen con la cedula del usuario
	//}
	
	$data_update['Usuario_Nombre'] = mysql_real_escape_string($nombre_usuario);
	$data_update['Usuario_Apellidos'] = mysql_real_escape_string($apellidos_usuario);
	$data_update['Usuario_Celular'] = mysql_real_escape_string($celular_usuario);
	$data_update['Usuario_Telefono'] = mysql_real_escape_string($telefono_usuario);
	$data_update['Usuario_Imagen_URL'] = mysql_real_escape_string($this->direccion_url_imagen);
	$data_update['Usuario_Correo_Electronico'] = mysql_real_escape_string($email_usuario);
	if($usuario_password && $this->user->login($data['Usuario_Nombre_Usuario'], $usuario_password_actual)){
		$data_update['Usuario_Password'] = MD5($usuario_password);     
	}
	$this->user->actualizar($codigo_usuario, $data_update);
	
	$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario cambio su perfil: ".mysql_real_escape_string($codigo_usuario),$data['Sucursal_Codigo'],'edicion');
	
	redirect('home', 'location');
 }



 function do_upload($cedula)
    {
		//echo $cedula;
       //especificamos a donde se va almacenar nuestra imagen
        $config['upload_path'] = 'application/images/User_Photos';
        //indicamos que tipo de archivos están permitidos
        $config['allowed_types'] = 'jpg|png';
        //indicamos el tamaño maximo permitido en este caso 1M
        $config['max_size'] = '5000';
        //le indicamos el ancho maximo permitido
        $config['max_width']  = '5000';
        //le indicamos el alto maximo permitodo
        $config['max_height']  = '5000';
        //Ponemos Nombre al archivo deseado
        $config['file_name']  = $cedula;
        //cargamos nuestra libreria con nuestra configuracion
        $config['overwrite'] = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload())
        {
        	if($this->flag){	
        		$this->direccion_url_imagen = "Default.png";
        	}
        	else{
				$this->direccion_url_imagen = $this->nombreImagenTemp;        			
        	}
        }  
        else
        {
        	$data = array('upload_data' => $this->upload->data());
       
            foreach ($this->upload->data() as $item => $value){               
				if($item=="file_path"){
					$path=$value; 
				}if($item=="file_name"){
					$name=$value;
				}            
            }// end foreach
        	//$this->mensaje ="Imagen subida correctamente"; 
        	$this->redimencionarImagen($path,$name);	
        }
    }  

    function redimencionarImagen($path,$name){
    	$config['image_library'] = 'gd2';
		$config['source_image']	= $path.$name; // le decimos donde esta la imagen que acabamos de subir
		$config['new_image']=$path."/thumb";
		//$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		$config['quality'] = '100%';    // calidad de la imagen
		$config['width']	 = $this->ancho_Imagen;
		$config['height']	= $this->alto_Imagen;
		$this->load->library('image_lib', $config);	
		if (!$this->image_lib->resize())
		{
			//$this->mensaje = $this->mensaje." error -> ".$this->image_lib->display_errors();
		}
        $this->direccion_url_imagen = $name;
        $this->image_lib->resize();
    }

 
 
 
	
 }// FIN DE LA CLASE


?>