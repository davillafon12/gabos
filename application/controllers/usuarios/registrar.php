<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class registrar extends CI_Controller {


	private $mensaje; 
	private $ancho_Imagen= 100;
	private $alto_Imagen=100;
	private $direccion_Url_Imagen = " ";
	private $monto_minimo_defecto =10000;
	private $monto_maximo_defecto =800000;
	private $calidad_Cliente = 5;


	 function __construct()
	 {
	    parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);

	 }

	 function index()
	 {
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		
		if($permisos['registrar_usuarios'])
		{
		$this->load->helper(array('form')); 
		$empresas_actuales = $this->empresa->get_empresas_ids_array();
		$data['Familia_Empresas'] = $empresas_actuales;
		$this->load->view('usuarios/usuarios_registrar_view', $data);	
		}
		else{
		   redirect('accesoDenegado', 'location');
		}
	}

	 function es_Cedula_Utilizada()
	 {
		$id_request=$_GET['id'];
		//$id_request=1;
		//include '/../../models/empresa.php';
		$ruta_base_imagenes_script = base_url('application/images/scripts');

		if($this->user->existe_Usuario_Cedula($id_request))
		{
			echo "true"; //echo "<img src=".$ruta_base_imagenes_script."/error.gif />";
		}
		else
		{
			echo "false"; //echo "<img src=".$ruta_base_imagenes_script."/tick.gif />";
		}
	 }
	 function es_Nombre_Utilizada()
	 {
		$id_request=$_GET['id'];
		//$id_request=1;
		//include '/../../models/empresa.php';
		$ruta_base_imagenes_script = base_url('application/images/scripts');

		if($this->user->existe_Nombre_Usuario($id_request))
		{
			echo "true"; //echo "<img src=".$ruta_base_imagenes_script."/error.gif />";
		}
		else
		{
			echo "false"; //echo "<img src=".$ruta_base_imagenes_script."/tick.gif />";
		}
	 }	 

	function registrar_Usuarios(){
		

		$codigo = $this->user->getCantidadUsuarios()+1; 
		$nombre_usuario = $this->input->post('nombre_usuario');
		$apellidos_usuario = $this->input->post('apellidos_usuario');
		$cedula_usuario = $this->input->post('cedula_usuario');
		$tipo_cedula_usuario = $this->input->post('tipo_cedula_usuario');
		$celular_usuario = $this->input->post('celular_usuario');
		$telefono_usuario = $this->input->post('telefono_usuario');
		$sucursal = $this->input->post('sucursal');
		
		date_default_timezone_set("America/Costa_Rica");
	    $fecha_ingreso_usuario = date("y/m/d : H:i:s", now());
		//$fecha_ingreso_usuario = $this->input->post('fecha_ingreso_usuario');
		$fecha_cesantia_usuario = "NULL"; 
		$fecha_recontratacion_Usuario = "NULL"; 
		$usuario_nombre_usuario = $this->input->post('usuario_nombre_usuario');
		$observaciones = $this->input->post('observaciones');
		$usuario_password = $this->input->post('usuario_password');
		$email_usuario = $this->input->post('email_usuario');
		$rango_usuario = $this->input->post('usuario_rango');
		$this->do_upload($cedula_usuario."_".$sucursal); // metodo encargado de cargar la imagen con la cedula del usuario

		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$ruta_base_imagenes_script = base_url('application/images/scripts');

	    if($this->user->registrar($codigo, $nombre_usuario, $apellidos_usuario, $cedula_usuario, $tipo_cedula_usuario,  $celular_usuario, $telefono_usuario, $fecha_ingreso_usuario, $fecha_cesantia_usuario, $fecha_recontratacion_Usuario, $usuario_nombre_usuario, $observaciones, $usuario_password, $this->direccion_url_imagen, $email_usuario, $rango_usuario, $sucursal))
		{ //Si se ingreso bien a la BD
			if(isset($_POST['permisos'])){
				foreach($_POST['permisos'] as $permiso){
					//echo $permiso;
					$this->user->agregarPermiso($codigo, $sucursal, $permiso, 1);
				}			
			}			
			
			//Titulo de la pagina
			$data['Titulo_Pagina'] = "Transacción Exitosa";
		
			$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ingreso el usuario ".mysql_real_escape_string($nombre_usuario)." codigo: ".$codigo,$data['Sucursal_Codigo'],'registro');
		    $data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>El ingreso del Usuario ".$nombre_usuario." fue exitoso! <img src=".$ruta_base_imagenes_script."/tick.gif /></p></div><br>
			                         <div class='Informacion'>
						             <form action=".base_url('usuarios/registrar').">
					                 				
									 <p class='titles'>Datos del Usuario:</p><br><hr>
									 <img src=".base_url('application/images/User_Photos/thumb/'.$this->direccion_url_imagen)." alt=\"Smiley face\" height=\"100\" width=\"100\"><br>
									 <p class='titles'>-Nombre:</p> <p class='content'>".$nombre_usuario." ".$apellidos_usuario.".</p><br>
									 <p class='titles'>-Cédula:</p> <p class='content'>".$cedula_usuario.".</p><br>
									 <p class='titles'>-Tipo Cédula:</p> <p class='content'>".$tipo_cedula_usuario.".</p><br>
									 <p class='titles'>-Celular:</p> <p class='content'>".$celular_usuario.".</p><br>
									 <p class='titles'>-Telefono:</p> <p class='content'>".$telefono_usuario.".</p><br>
									 <p class='titles'>-Fecha Ingreso:</p> <p class='content'>".$fecha_ingreso_usuario.".</p><br>
									 <p class='titles'>-Nombre Usuario:</p> <p class='content'>".$usuario_nombre_usuario.".</p><br>
									 <p class='titles'>-Email:</p> <p class='content'>".$email_usuario.".</p><br>	
									 <p class='titles'>-Rango:</p> <p class='content'>".$rango_usuario.".</p><br>
									 <p class='titles'>-Empresa:</p> <p class='content'>".$this->empresa->getNombreEmpresa($sucursal).".</p><br>
									 <p class='titles'>-Observaciones:</h3> </p><br><p class='content_ob'>
									 ".$observaciones.".</p>
									 <input class='buttom' tabindex='4' value='Registrar otro Usuario' type='submit' >
					                 </form>
									 </div>";
			$this->load->view('usuarios/view_informacion_guardado', $data);
		}
		else
		{ //Hubo un error  no se ingreso a la BD
			//echo "fallo esta mierda"; 
			//echo "la cedula es : ".$cedula_usuario; 
		}
			/*$data['Titulo_Pagina'] = "Transacción Fallida";
			$data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>Hubo un error al ingresar el usuario ".$nombre_usuario."! <img src=".$ruta_base_imagenes_script."/error.gif /></p></div><br>
			                         <div class='Informacion'>								 
						             <form action=".base_url('usuarios/registrar').">
										 <input class='buttom' tabindex='2' value='Registrar otro Usuario' type='submit' >
					                 </form>
									 </div>";
			$this->load->view('usuarios/view_informacion_guardado', $data);
		}*/
		
	}

	function do_upload($cedula)
    {

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
        $this->load->library('upload', $config);
        //verificamos si existe errores
        //$this->upload->do_upload($field_name);
        //$field_name= $id_nombre; 

        if (!$this->upload->do_upload())
        {
        	$this->direccion_url_imagen = "Default.png";
        	//$this->redimencionarImagen(base_url("application/images/Client_Photo"),"Default.png");
            //almacenamos el error que existe
            //$this->mensaje = "Hubo un error subiendo la imagen :". $this->upload->display_errors();
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

    function registro_masivo(){
    		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
			if($permisos['registrar_usuarios_masivo'])
			{	
			    $this->load->view('clientes/registro_masivo_clientes', $data);	
			}
			else{
			   redirect('accesoDenegado', 'location');
			}	

    }
	
	function agregarPermiso($usuario, $sucursal, $area, $valor){
		include('permisos.php');
		if(in_array($area, $permisos)){ //Si el permiso esta dentro de la lista de permisos
			$this->user->agregarPermiso($usuario, $sucursal, $area, $valor);
		}
	}

}

?>