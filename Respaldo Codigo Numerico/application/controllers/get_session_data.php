<?php

	if(!isset($_SESSION)){session_start();}
	
	if($this->session->userdata('logged_in'))
	{
		$session_data = $this->session->userdata('logged_in');
		$data['Usuario_Nombre_Usuario'] = $session_data['Usuario_Nombre_Usuario'];
		$data['Usuario_Codigo'] = $session_data['Usuario_Codigo'];
		$data['Sucursal_Codigo'] = $session_data['Sucursal_Codigo'];
		$data['Usuario_Imagen_URL'] = $session_data['Usuario_Imagen_URL'];
		$data['Usuario_Nombre'] = $session_data['Usuario_Nombre'];
		$data['Usuario_Apellidos'] = $session_data['Usuario_Apellidos'];
		$data['Usuario_Observaciones'] = $session_data['Usuario_Observaciones'];
		$data['Usuario_Rango'] = $session_data['Usuario_Rango'];
		//$permisos_array = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		//$data['Permisos'] = $permisos_array;
		//print_r ($permisos_array);
	}
	else
	{
		//If no session, redirect to login page
		redirect('login', 'refresh');
	}
?>