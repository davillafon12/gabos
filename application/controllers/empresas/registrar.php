<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class registrar extends CI_Controller {
    
 function __construct()
 {
    parent::__construct(); 
	$this->load->model('user','',TRUE);
	$this->load->model('empresa','',TRUE);
	$this->load->model('cliente','',TRUE);
        $this->load->model('ubicacion','',TRUE);
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
	
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if(!$permisos['registrar_empresa'])
	{	
	   redirect('accesoDenegado', 'location');
	}
 }

 function index()
 {
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		
	/*$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if($permisos['registrar_empresa'])
	{	
	   $this->load->helper(array('form'));
	   $this->load->view('empresas/registrar_view_empresas', $data);	
	}
	else{
	   redirect('accesoDenegado', 'location');
	}*/
        $data['javascript_cache_version'] = $this->javascriptCacheVersion;
        $data['tiposIdentificacion'] = $this->tiposIdentificacion;
        $data['provincias'] = $this->ubicacion->getProvincias();
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
        $tipo_identificacion = $this->input->post('tipo_identificacion');
	$nombre_empresa = $this->input->post('name');
	$telefono_empresa = $this->input->post('telefono');
	$fax_empresa = $this->input->post('fax');
        $cod_telefono_empresa = $this->input->post('cod_tel');
	$cod_fax_empresa = $this->input->post('cod_fax');
	$email_empresa = $this->input->post('email');
	$observaciones_empresa = $this->input->post('observaciones');
	$direccion_empresa = $this->input->post('direccion');
	$administrador_empresa = $this->input->post('administrador');
	$leyenda_tributacion = $this->input->post('leyenda');
	$cliente_liga = trim($this->input->post("cliente_liga_id"));
        
        $user_tributa = trim($this->input->post("user_tributa"));
        $pass_tributa = trim($this->input->post("pass_tributa"));
        $ambiente_tributa = trim($this->input->post("ambiente_tributa"));
        $pin_tributa = trim($this->input->post("pin_tributa"));
        
        $provincia = trim($this->input->post("provincia"));
        $canton = trim($this->input->post("canton"));
        $distrito = trim($this->input->post("distrito"));
        $barrio = trim($this->input->post("barrio"));
        
        $codigo_actividad = trim($this->input->post("codigo_actividad"));
	
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
	
	$id_empresa= $this->empresa->getCantidadEmpresas();
	//echo $id_empresa;
	$nombre = $this->user->get_name($data['Usuario_Codigo']);
	//echo $nombre;
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	if($this->empresa->registrar(   $id_empresa, 
                                        $nombre_empresa, 
                                        $telefono_empresa, 
                                        $observaciones_empresa, 
                                        $direccion_empresa, 
                                        $nombre, 
                                        $administrador_empresa, 
                                        $leyenda_tributacion, 
                                        $cedula_empresa, 
                                        $fax_empresa, 
                                        $email_empresa, 
                                        $user_tributa,
                                        $pass_tributa,
                                        $ambiente_tributa,
                                        $pin_tributa,
                                        $tipo_identificacion,
                                        $cod_telefono_empresa,
                                        $cod_fax_empresa,
                                        $provincia,
                                        $canton,
                                        $distrito,
                                        $barrio,
                                        $codigo_actividad))
	{ //Si se ingreso bien a la BD
			$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ingreso la empresa ".mysql_real_escape_string($nombre_empresa)." codigo: ".$id_empresa,$data['Sucursal_Codigo'],'registro');
			
			if($cliente_liga != ""){
				if($this->cliente->existe_Cliente($cliente_liga)){
					//Guardamos la liga de la empresa con su cliente
					$this->empresa->registrarClienteConEmpresaLiga($id_empresa, $cliente_liga);
				}
			}
			
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