<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class registrar extends CI_Controller {

 function __construct()
 {
    parent::__construct(); 
	$this->load->model('user','',TRUE);
	$this->load->model('empresa','',TRUE);
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if(!$permisos['registrar_empresa'])
	{	
	   redirect('accesoDenegado', 'location');
	}
 }

 function index()
 {
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		
	/*$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if($permisos['registrar_empresa'])
	{	
	   $this->load->helper(array('form'));
	   $this->load->view('empresas/registrar_view_empresas', $data);	
	}
	else{
	   redirect('accesoDenegado', 'location');
	}*/
	$this->load->helper(array('form'));
	$this->load->view('empresas/registrar_view_empresas', $data);
 }
 
 function es_codigo_usado()
 {
	$id_request=$_GET['id'];
	//$id_request=1;
	//include '/../../models/empresa.php';
	$ruta_base_imagenes_script = base_url('application/images/scripts');

	if($this->empresa->es_codigo_usado($id_request))
	{
		echo "true"; //echo "<img src=".$ruta_base_imagenes_script."/error.gif />";
	}
	else
	{
		echo "false"; //echo "<img src=".$ruta_base_imagenes_script."/tick.gif />";
	}
 }
 
 function registraEmpresa()
 {
	//$id_empresa = $this->input->post('codigo');
	$cedula_empresa = $this->input->post('cedula_ju');
	$nombre_empresa = $this->input->post('name');
	$telefono_empresa = $this->input->post('telefono');
	$fax_empresa = $this->input->post('fax');
	$email_empresa = $this->input->post('email');
	$observaciones_empresa = $this->input->post('observaciones');
	$direccion_empresa = $this->input->post('direccion');
	$administrador_empresa = $this->input->post('administrador');
	$leyenda_tributacion = $this->input->post('leyenda');
	
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
	
	$id_empresa= $this->empresa->getCantidadEmpresas();
	//echo $id_empresa;
	$nombre = $this->user->get_name($data['Usuario_Codigo']);
	//echo $nombre;
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	if($this->empresa->registrar($id_empresa, $nombre_empresa, $telefono_empresa, $observaciones_empresa, $direccion_empresa, $nombre, $administrador_empresa, $leyenda_tributacion, $cedula_empresa, $fax_empresa, $email_empresa))
	{ //Si se ingreso bien a la BD
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ingreso la empresa ".mysql_real_escape_string($nombre_empresa)." codigo: ".$id_empresa,$data['Sucursal_Codigo'],'registro');

		//Titulo de la pagina
		/*$data['Titulo_Pagina'] = "Transacción Exitosa";
	     
		//$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ingreso la empresa ".mysql_real_escape_string($nombre_empresa)." codigo: ".$id_empresa,$data['Sucursal_Codigo'],'registro');
	    $data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>El ingreso de la empresa ".$nombre_empresa." fue exitoso! <img src=".$ruta_base_imagenes_script."/tick.gif /></p></div><br>
		                         <div class='Informacion'>
					             <form action=".base_url('empresas/editar').">
				                 				
								 <p class='titles'>Datos de la empresa:</p><br><hr>
								 <p class='titles'>-Codigo:</p> <p class='content'>".$id_empresa.".</p><br>
								 <p class='titles'>-Nombre:</p> <p class='content'>".$nombre_empresa.".</p><br>
								 <p class='titles'>-Dirección:</p> <p class='content'>".$direccion_empresa.".</p><br>
								 <p class='titles'>-Administrador(a):</p> <p class='content'>".$administrador_empresa.".</p><br>
								 <p class='titles'>-Observaciones:</h3> </p><br><p class='content_ob'>
								 ".$observaciones_empresa.".</p>
								 <input class='buttom' tabindex='6' value='Aceptar' type='submit' >
				                 </form>
								 </div>";
		$this->load->view('empresas/view_informacion_guardado', $data);*/
		redirect('empresas/editar', 'location');
	}
	else
	{ //Hubo un error  no se ingreso a la BD
		$data['Titulo_Pagina'] = "Transacción Fallida";
		$data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>Hubo un error al ingresar la empresa ".$nombre_empresa."! <img src=".$ruta_base_imagenes_script."/error.gif /></p></div><br>
		                         <div class='Informacion'>								 
					             <form action=".base_url('empresas/registrar').">
									 <input class='buttom' tabindex='2' value='Registrar otra empresa' type='submit' >
				                 </form>
								 </div>";
		$this->load->view('empresas/view_informacion_guardado', $data);
	}
	
	
 }
 
 
 
 


}

?>