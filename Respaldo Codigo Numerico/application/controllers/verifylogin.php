<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VerifyLogin extends CI_Controller {
	public $sess_array = array();
	function __construct()
		parent::__construct();
		$this->load->model('user','',TRUE);
	}

	function index()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Usuario', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password', 'Contrase&ntilde;a', 'trim|required|xss_clean|callback_check_database');
		if($this->form_validation->run() == FALSE)
			$this->load->view('login_view');
		}else{
			if($this->empresa->isActivated($data['Sucursal_Codigo']))
		}
	}

 function check_database($password)
 {
   //Field validation succeeded.&nbsp; Validate against database
   $username = $this->input->post('username');

   //query the database
   $result = $this->user->login($username, $password);

   if($result)
   {     
     //$sess_array = array();
     foreach($result as $row)
     {	    
         $sess_array = array(
         'Usuario_Codigo' => $row->Usuario_Codigo,
         'Usuario_Nombre_Usuario' => $row->Usuario_Nombre_Usuario,		 
		 'Sucursal_Codigo' => $row->TB_02_Sucursal_Codigo,
		 'Usuario_Imagen_URL' => $row->Usuario_Imagen_URL,
		 'Usuario_Nombre' => $row->Usuario_Nombre,
		 'Usuario_Apellidos' => $row->Usuario_Apellidos,
		 'Usuario_Observaciones' => $row->Usuario_Observaciones,
       );
       $this->session->set_userdata('logged_in', $sess_array);
	   //Guardar transaccion de logueo
	   $this->user->guardar_transaccion($sess_array['Usuario_Codigo'], 'El usuario se logueo al sistema',$sess_array['Sucursal_Codigo'],'login');
     }	 
     return TRUE;
   }
   else
   {
     $ruta_base_imagenes_script = base_url('application/images/scripts');
     $this->form_validation->set_message('check_database', "<div class='Error'><img src=".$ruta_base_imagenes_script."/error.gif />Usuario o contrase&ntilde;a incorrectos</div>");
     return false;
   }
 }
}
?>