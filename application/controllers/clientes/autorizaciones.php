<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class autorizaciones extends CI_Controller {
	 function __construct()
	 {
	    parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('cliente','',TRUE);	
		
	 }

	 function index()
	 {
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		if($permisos['editar_autorizacion'])
		{
			$this->load->helper(array('form'));
			$this->load->view('clientes/clientes_autorizaciones_edicion_view', $data);	
		}
		else{
		   redirect('accesoDenegado', 'location');
		}		
	}
	
	function sendErrorActualizar($mensaje){
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		if($permisos['editar_autorizacion'])
		{
			$this->load->helper(array('form'));
			$data['error'] = $mensaje;
			$this->load->view('clientes/clientes_autorizaciones_edicion_view', $data);	
		}
		else{
		   redirect('accesoDenegado', 'location');
		}
	}
	
	function success(){
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		if($permisos['editar_autorizacion'])
		{
			$this->load->helper(array('form'));
			$data['success'] = 1;
			redirect('clientes/autorizaciones', 'refresh');		
			//$this->load->view('clientes/clientes_autorizaciones_edicion_view', $data);	
		}
		else{
		   redirect('accesoDenegado', 'location');
		}
	}
	
	function actualizar(){
		//print_r($_POST);
		//print_r($_FILES['carta']);
		//echo $_FILES['carta']['name'][0];
		if(isset($_POST['cedula_persona_uno'])
			&&isset($_POST['nombre_persona_uno'])
			&&isset($_POST['apellido_persona_uno'])
			&&isset($_POST['cedula_persona_dos'])
			&&isset($_POST['nombre_persona_dos'])
			&&isset($_POST['apellido_persona_dos'])			
			&&isset($_POST['cedula'])
			&&isset($_FILES['carta'])){
			
			$cedula = $_POST['cedula'];
			
			if($this->cliente->existe_Cliente($cedula)){			
				$persona_uno = array(
										'cedula'=>$_POST['cedula_persona_uno'],
										'nombre'=>$_POST['nombre_persona_uno'],
										'apellido'=>$_POST['apellido_persona_uno']
									);
				$persona_dos = array(
										'cedula'=>$_POST['cedula_persona_dos'],
										'nombre'=>$_POST['nombre_persona_dos'],
										'apellido'=>$_POST['apellido_persona_dos']
									);
				if($resultado = $this->guardarAutorizacion($persona_uno, $cedula, 1)){
					if($resultado == 'agrego'){
						if($_FILES['carta']['name'][0]==''){
							//No proceso la imagen
							$this->cliente->actualizarImagenAutorizacion($cedula, 1, 'default.png');
						}else{
							$nombre = $this->guardarImagen(1, $cedula);
							$this->cliente->actualizarImagenAutorizacion($cedula, 1, $nombre);
						}
					}elseif($resultado == 'actualizo'){
						//echo "Actualiza";
						if($_FILES['carta']['name'][0]==''){
							//echo "Nombre vacio";
						}else{
							$nombre = $this->guardarImagen(1, $cedula);
							$this->cliente->actualizarImagenAutorizacion($cedula, 1, $nombre);
							//echo "Guardo Imagen";
						}
					}elseif($resultado){ //Valor true
						//No se hizo nada
						//echo "Hacer nada";
					}
					
				}
				else{
					//ERROR AL INGRESAR O ACTUALIZAR AUTORIZACION
					$this->sendErrorActualizar("Error actualizando la autorización 1, contacte al Administrador");
				}
				if($this->guardarAutorizacion($persona_dos, $cedula, 2)){
					if($resultado == 'agrego'){
						if($_FILES['carta']['name'][1]==''){
							//No proceso la imagen
							$this->cliente->actualizarImagenAutorizacion($cedula, 2, 'default.png');
						}else{
							$nombre = $this->guardarImagen(2, $cedula);
							$this->cliente->actualizarImagenAutorizacion($cedula, 2, $nombre);
						}
					}elseif($resultado == 'actualizo'){
						if($_FILES['carta']['name'][1]==''){
							
						}else{
							$nombre = $this->guardarImagen(2, $cedula);
							$this->cliente->actualizarImagenAutorizacion($cedula, 2, $nombre);
						}
					}elseif($resultado){ //Valor true
						//No se hizo nada
					}
				}
				else{
					//ERROR AL INGRESAR O ACTUALIZAR AUTORIZACION
					$this->sendErrorActualizar("Error actualizando la autorización 2, contacte al Administrador");
				}
			}else{
				//ERROR NO EXISTE CLIENTE
				$this->sendErrorActualizar("¡El cliente no existe!");
			}					
		}else{
			//ERROR EN EL ENVIO DE LA URL
			$this->sendErrorActualizar("Error en la estructura de la URL, contacte al Administrador");
		}	
		$this->success();
	}
	
	function verificarSiYaTieneAutorizacion($cedula, $secuencia){
		if($this->cliente->verificarSiYaTieneAutorizacion($cedula, $secuencia)){return true;}
		else{return false;}
	}
	
	function verificarSiSeDebeCambiar($valores){ 
	//Me indica que si algun campo es vacio no debo cambiar nada, 
	//esto exige a que todos los campos esten llenos para actualizar info
		foreach($valores as $valor){
			if(trim($valor)==''){return false;}
		}
		return true;
	}
	
	function verificarSiSeDebeEliminar($valores){ 
	//Me indica que si todos los campos son vacios y eso 
	//significa que debo eleiminar esa autorizacion 
		
		foreach($valores as $valor){
			if(trim($valor)!=''){return false;}
		}
		return true;
	}
	
	function guardarAutorizacion($persona, $cedula, $secuencia){
		$accion = true;
		if($this->verificarSiSeDebeEliminar($persona)){
				$this->cliente->eliminarAutorizacionCliente($secuencia, $cedula);
				$accion = "elimino";
		}else{
				if($this->verificarSiSeDebeCambiar($persona)){	
					$persona['secuencia'] = $secuencia;  //Lo metemos en persona para procesarlo en cliente
					if($this->verificarSiYaTieneAutorizacion($cedula, $secuencia))
					{
						$this->cliente->actualizarAutorizacion($persona, $cedula);
						$accion = 'actualizo';
					}else{			
						$this->cliente->agregarAutorizacion($persona, $cedula);
						$accion = "agrego";
					}
				}
		}
		return $accion;
	}
	
	function guardarImagen($secuencia, $cedula){
		$nombre_carta = $cedula."_auth_$secuencia.png";
		if($_FILES['carta']['size'][$secuencia-1] < (1024000)) 
		{			
			if($_FILES['carta']['type'][$secuencia-1] == 'image/png'){
				move_uploaded_file($_FILES['carta']['tmp_name'][$secuencia-1], 'application/images/cartas/'.$nombre_carta);
				return $nombre_carta;
			}else{
				//ERROR IMAGEN MAS GRANDE
				$this->sendErrorActualizar("No se subió la carta #$secuencia, pues esta en un formato indebido");
			}
		}else{
			//ERROR IMAGEN MAS GRANDE
			$this->sendErrorActualizar("No se subió la carta #$secuencia, pues es mayor a lo permitido (1MB)");
		}
	}
	
	function getCliente(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['cedula'])){
			$cedula = $_POST['cedula']; 
			if(trim($cedula) == '1' || trim($cedula) == '0'){
				$retorno['error'] = '4'; //Error cliente contado y afiliado
			}else{
				if($clienteArray = $this->cliente->getClientes_Cedula($cedula)){
					//$retorno['status'] = 'success';
					foreach($clienteArray as $row){
						$cliente['nombre'] = $row-> Cliente_Nombre;
						$cliente['apellidos'] = $row-> Cliente_Apellidos;						
					}
					include '/../get_session_data.php';
					
					$autorizado1 = $this->cliente->verificarSiYaTieneAutorizacion($cedula, 1); 
					$autorizado2 = $this->cliente->verificarSiYaTieneAutorizacion($cedula, 2);
					
					$persona1 = array(
											'cedula'=>'',
											'nombre'=>'',
											'apellidos'=>'',
											'carta'=>''
											);
					$persona2 = array(
											'cedula'=>'',
											'nombre'=>'',
											'apellidos'=>'',
											'carta'=>''
											);
											
					
					if($autorizado1){
						foreach($autorizado1 as $auto)
						{
							$persona1 = array(
												'cedula'=>$auto->AuthClientes_Cedula,
												'nombre'=>$auto->AuthClientes_Nombre,
												'apellidos'=>$auto->AuthClientes_Apellidos,
												'carta'=>$auto->AuthClientes_Carta_URL
												);
						}
					}
					if($autorizado2){
						foreach($autorizado2 as $auto)
						{
							$persona2 = array(
												'cedula'=>$auto->AuthClientes_Cedula,
												'nombre'=>$auto->AuthClientes_Nombre,
												'apellidos'=>$auto->AuthClientes_Apellidos,
												'carta'=>$auto->AuthClientes_Carta_URL
												);
						}
					}
					$cliente['persona1'] = $persona1;
					$cliente['persona2'] = $persona2;
					$retorno['cliente'] = $cliente;
					/// TODO SALIO BIEN
					$retorno['status'] = 'success';
					$retorno['cliente'] = $cliente;
										
				}else{
					$retorno['error'] = '3'; //Error no hay cliente
				}
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}
	
	
	function verAutorizaciones(){
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		if($permisos['ver_autorizacion'])
		{
			$this->load->helper(array('form'));
			$this->load->view('clientes/clientes_autorizaciones_ver_view', $data);	
		}
		else{
		   redirect('accesoDenegado', 'location');
		}
	}
	
	
}

?>