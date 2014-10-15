<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class paginaNoEncontrada extends CI_Controller {

 function __construct()
 {
   parent::__construct();
   $this->load->model('user','',TRUE);
 }

 function index()
 {
    include 'get_session_data.php'; //Esto es para traer la informacion de la sesion
    $this->load->view('error_404', $data);
 }
 
}

?>