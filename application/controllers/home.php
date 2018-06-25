<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

 function __construct()
 {
   parent::__construct();
   $this->load->model('user','',TRUE);
 }

 function index(){
    include 'get_session_data.php'; //Esto es para traer la informacion de la sesion	
    $this->load->view('home_view', $data);
 }

 function logout()
 {
     include 'get_session_data.php';
    $session_data = $this->session->userdata('logged_in');
    require_once PATH_API_HACIENDA;
    $apife = new API_FE();
    $isUp = $apife->isUp();
    if($isUp){
        $resp = $apife->logOutUser($_SESSION["api_sessionkey"], $data["Usuario_Nombre_Usuario"]);
    }
    $Usuario_Id = $session_data['Usuario_Codigo'];
    $Usuario_Sucursal = $session_data['Sucursal_Codigo'];
    $this->user->guardar_transaccion($Usuario_Id, 'El usuario salio del sistema',$Usuario_Sucursal, 'login');
    $this->session->unset_userdata('api_sessionkey');
    $this->session->unset_userdata('api_sessionup');
    $this->session->unset_userdata('logged_in');
    $this->session->sess_destroy();
    redirect('home', 'location');
 }
 


}

?>

