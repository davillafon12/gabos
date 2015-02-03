<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

 function __construct()
 {
   parent::__construct();
   $this->load->model('user','',TRUE);
 }

 function index()
 {
    include 'get_session_data.php'; //Esto es para traer la informacion de la sesion	
	//$id_con = mysqli_connect('76.162.254.146', 'usuario_comercio', 'Comercio_666');
    $this->load->view('home_view', $data);
 }

 function logout()
 {
   $session_data = $this->session->userdata('logged_in');
   $Usuario_Id = $session_data['Usuario_Codigo'];
   $Usuario_Sucursal = $session_data['Sucursal_Codigo'];
   $this->user->guardar_transaccion($Usuario_Id, 'El usuario salio del sistema',$Usuario_Sucursal, 'login');
   $this->session->unset_userdata('logged_in');
   $this->session->sess_destroy();
   redirect('home', 'location');
 }
 


}

?>

