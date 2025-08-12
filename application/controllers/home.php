<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

   function __construct(){
      parent::__construct();
      $this->load->model('user','',TRUE);
   }

   function index(){
      $data = $this->userdata_wrap;
      $this->load->view('home_view', $data);
   }

   function logout(){
      $this->user->guardar_transaccion($this->userdata_wrap["Usuario_Codigo"], 'El usuario salio del sistema',$this->userdata_sucursal, 'logout');
      $this->destroyGaboSession();
      redirect('home', 'location');
   }

}

?>

