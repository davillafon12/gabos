<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class dempresa extends CI_Controller {

 private $sess_array = array();

 function __construct()

 {

   parent::__construct();

   $this->load->model('user','',TRUE);
   $this->load->model('empresa','',TRUE);

 }

 function index()

 {
	include 'get_session_data.php'; //Esto es para traer la informacion de la sesion
    $this->load->view('error_empresa_unabled', $data);
 }



}

?>