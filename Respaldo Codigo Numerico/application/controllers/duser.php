<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class duser extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->load->view('error_user_deactivated');
	}
}

?>