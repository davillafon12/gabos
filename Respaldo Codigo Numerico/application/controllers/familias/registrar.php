<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class registrar extends CI_Controller {

 function __construct()
 {
    parent::__construct(); 
	$this->load->model('user','',TRUE);
	$this->load->model('empresa','',TRUE);
	$this->load->model('familia','',TRUE);
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if(!$permisos['registrar_familia'])
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
	$empresas_actuales = $this->empresa->get_empresas_ids_array();
	$data['Familia_Empresas'] = $empresas_actuales;
	$this->load->view('familias/view_registrar_familia', $data);
 }
 
 function es_codigo_usado()
 {
	$id_request=$_GET['id'];
	//$id_request=1;
	//include '/../../models/empresa.php';
	$ruta_base_imagenes_script = base_url('application/images/scripts');

	if($this->familia->es_codigo_usado($id_request, $data['Sucursal_Codigo']))
	{
		echo "true"; //echo "<img src=".$ruta_base_imagenes_script."/error.gif />";
	}
	else
	{
		echo "false"; //echo "<img src=".$ruta_base_imagenes_script."/tick.gif />";
	}
 }
 
 
 function registraFamilia()
 {
	//$id_familia = $this->input->post('codigo');
	$nombre_familia = $this->input->post('name');
	//$descuento_familia = $this->input->post('descuento');
	$observaciones_familia = $this->input->post('observaciones');
	$sucursal_familia = $this->input->post('sucursal');
	
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
	
	$id_familia = $this->familia->getCantidadFamilias($sucursal_familia);
	
	
	//echo $id_familia." DDD";
	$nombre = $this->user->get_name($data['Usuario_Codigo']);
	//echo $nombre;
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	if($this->familia->registrar($id_familia, $nombre_familia, $observaciones_familia, $sucursal_familia, $nombre))
	{ //Si se ingreso bien a la BD
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ingreso la familia ".mysql_real_escape_string($nombre_familia)." codigo: ".mysql_real_escape_string($id_familia),$data['Sucursal_Codigo'],'registro');

		//Titulo de la pagina
		//echo "PASO POR AQUI";
		/*$data['Titulo_Pagina'] = "Transacción Exitosa";
	
	    $nombre_empresa = $this->empresa->getNombreEmpresa($sucursal_familia);
		$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ingreso la familia ".mysql_real_escape_string($nombre_familia)." codigo: ".mysql_real_escape_string($id_familia),$data['Sucursal_Codigo'],'registro');
	    $data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>El ingreso de la empresa ".$nombre_familia." fue exitoso! <img src=".$ruta_base_imagenes_script."/tick.gif /></p></div><br>
		                         <div class='Informacion'>
					             <form action=".base_url('familias/familias').">
				                 				
								 <p class='titles'>Datos de la familia:</p><br><hr>
								 <p class='titles'>-Codigo:</p> <p class='content'>".$id_familia.".</p><br>
								 <p class='titles'>-Nombre:</p> <p class='content'>".$nombre_familia.".</p><br>
								 <p class='titles'>-Descuento:</p> <p class='content'>".$descuento_familia."%.</p><br>
								 <p class='titles'>-Empresa:</p> <p class='content'>".$sucursal_familia." - ".$nombre_empresa.".</p><br>
								 <p class='titles'>-Observaciones:</h3> </p><br><p class='content_ob'>
								 ".$observaciones_familia.".</p>
								 <input class='buttom' tabindex='6' value='Aceptar' type='submit' >
				                 </form>
								 </div>";
		$this->load->view('empresas/view_informacion_guardado', $data);*/
		redirect('familias/familias', 'location');
	}
	else
	{ //Hubo un error  no se ingreso a la BD
		$data['Titulo_Pagina'] = "Transacción Fallida";
		$data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>Hubo un error al ingresar la familia ".$nombre_familia."! <img src=".$ruta_base_imagenes_script."/error.gif /></p></div><br>
		                         <div class='Informacion'>								 
					             <form action=".base_url('familias/familias').">
									 <input class='buttom' tabindex='2' value='Volver' type='submit' >
				                 </form>
								 </div>";
		$this->load->view('empresas/view_informacion_guardado', $data);
	}
	
	
 }
 
 
 
 


}

?>