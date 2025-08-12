<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class VerifyLogin extends CI_Controller {

	public $sess_array = array();

	function __construct(){
		parent::__construct();
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);
	}


	function index(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Usuario', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password', 'Contrase&ntilde;a', 'trim|required|xss_clean|callback_check_and_initialize');
		$ruta_base_imagenes_script = base_url('application/images/scripts');
		$errorMsg = "";

		// Validaciones basicas
		if($this->form_validation->run()){
			$username = $this->input->post('username');
			$password = $this->input->post('password');

			//Validamos que sea usuario con contraseña correcta
			$userData = $this->user->login($username, $password);
			if(is_array($userData) && sizeof($userData>0)){
				$userData = $userData[0];
				$sucursal = $this->empresa->getEmpresa($userData->TB_02_Sucursal_Codigo);
				if(is_array($sucursal) && sizeof($sucursal) > 0){
					$sucursal = $sucursal[0];

					//Validamos la sucursal que este activa
					if($sucursal->Sucursal_Estado){

						//Validamos que el usuario este activo
						if($userData->Usuario_Fecha_Cesantia === NULL){
							$datosFromateados = $this->formatearDataDeSession($userData, $sucursal);

							session_start();
							$_SESSION["usuario"] = $datosFromateados;

							//Bien nos vamos a home
							redirect('home', 'refresh');
						}else{
							redirect('duser', 'refresh');
						}
					}else{
						redirect('dempresa', 'refresh');
					}
				}else{
					$errorMsg = "Error al cargar la empresa";
				}
			}else{
				$errorMsg = "Usuario y/o contraseña incorrectos";
			}
		}else{
			$errorMsg = "Usuario y/o contraseña inválidos";
		}

		$this->form_validation->set_message('check_database', "<div class='Error'><img src=".$ruta_base_imagenes_script."/error.gif />$errorMsg</div>");
		$this->load->view('login_view');
	}

	private function formatearDataDeSession($usuarioData, $sucursalData){
		return array(
				'Usuario_Codigo' => $usuarioData->Usuario_Codigo,
				'Usuario_Nombre_Usuario' => $usuarioData->Usuario_Nombre_Usuario,
				'Sucursal_Codigo' => $usuarioData->TB_02_Sucursal_Codigo,
				'Usuario_Imagen_URL' => $usuarioData->Usuario_Imagen_URL,
				'Usuario_Nombre' => $usuarioData->Usuario_Nombre,
				'Usuario_Apellidos' => $usuarioData->Usuario_Apellidos,
				'Usuario_Observaciones' => $usuarioData->Usuario_Observaciones,
				'Usuario_Rango' => $usuarioData->Usuario_Rango,
				'javascript_cache_version' => $this->javascriptCacheVersion,
				'sucursalWrap' => array(
					'imagen_logo' => $sucursalData->Logo
				)
			);
	}

}

?>

