
<?php
Class User extends CI_Model
{
 function login($username, $password)
 {
   $this -> db -> select('Usuario_Codigo, Usuario_Cedula, Usuario_Nombre_Usuario, Usuario_Password, TB_02_Sucursal_Codigo, Usuario_Imagen_URL, Usuario_Nombre, Usuario_Apellidos, Usuario_Observaciones, Usuario_Rango');
   $this -> db -> from('TB_01_Usuario');
   $this -> db -> where('Usuario_Nombre_Usuario', mysql_real_escape_string($username));
   $this -> db -> where('Usuario_Password', MD5(mysql_real_escape_string($password)));
   $this -> db -> limit(1);

   $query = $this -> db -> get();

   if($query -> num_rows() == 1)
   {
     return $query->result();
   }
   else
   {
     return false;
   }
 }
 
function existe_Usuario_Cedula($cedula){
	$this -> db -> select('Usuario_Cedula');
	$this -> db -> from('TB_01_Usuario');
	$this -> db -> where('Usuario_Cedula', mysql_real_escape_string($cedula));
	$this -> db -> limit(1);

	$query = $this -> db -> get();

	if($query -> num_rows() != 0)
	{
	  return $query->result();
	}
	else
	{
	  return false;
	}
}	

function existe_Nombre_Usuario($nombre){
	$this -> db -> select('Usuario_Nombre_Usuario');
	$this -> db -> from('TB_01_Usuario');
	$this -> db -> where('Usuario_Nombre_Usuario', mysql_real_escape_string($nombre));
	$this -> db -> limit(1);

	$query = $this -> db -> get();

	if($query -> num_rows() != 0)
	{
	  return $query->result();
	}
	else
	{
	  return false;
	}
}	

 function guardar_transaccion($id_usuario,$descripcion,$id_sucursal,$tipo)
    {
	    date_default_timezone_set("America/Costa_Rica");
        $data = array(
                'Trans_Descripcion' => mysql_real_escape_string($descripcion),
                'Trans_Fecha_Hora' => date("y/m/d : H:i:s", now()),
				'Trans_Tipo' => mysql_real_escape_string($tipo),
				'Trans_IP' => $this->input->ip_address(),
                'TB_01_Usuario_Usuario_Codigo' => mysql_real_escape_string($id_usuario),
                'TB_01_Usuario_TB_02_Sucursal_Codigo' => mysql_real_escape_string($id_sucursal),
                );
        $this->db->insert('TB_12_Transacciones',$data);
    }
	
	function get_permisos($id_usuario, $id_sucursal)
	{
		$this -> db -> select('Permisos_Area, Permisos_Value');
		$this -> db -> from('tb_15_permisos');
		$this -> db -> where('TB_01_Usuario_Usuario_Codigo', mysql_real_escape_string($id_usuario));
		$this -> db -> where('TB_01_Usuario_TB_02_Sucursal_Codigo', mysql_real_escape_string($id_sucursal));
		$data = array(); // create a variable to hold the information
		
			
		
		$query = $this -> db -> get();
		
			$result = $query->result();
			foreach($result as $row)
			{			
			   $data[$row->Permisos_Area] = $row->Permisos_Value;  // add the row in to the results (data) array
			}
	   
	   return $data;  
    }
	
	
	function get_name($id_usuario)
	{
		//echo $id_usuario;
		$this -> db -> select('Usuario_Nombre_Usuario');
		$this -> db -> from('TB_01_Usuario');
		$this -> db -> where('Usuario_Codigo', mysql_real_escape_string($id_usuario));
		$this -> db -> limit(1);
		
		$query = $this -> db -> get();
		$result = $query->result();
		
		foreach($result as $row)
		{			
		   return $row->Usuario_Nombre_Usuario; 
		}
	}
	
	function getIdFromUserID($id_usuario, $sucursal)
	{
		//echo $id_usuario;
		$this -> db -> select('Usuario_Codigo');
		$this -> db -> from('TB_01_Usuario');
		$this -> db -> where('Usuario_Nombre_Usuario', mysql_real_escape_string($id_usuario));
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this -> db -> limit(1);
		
		$query = $this -> db -> get();
		$result = $query->result();
		
		foreach($result as $row)
		{			
		   return $row->Usuario_Codigo; 
		}
	}
	
	function registrar($codigo, $nombre, $apellidos, $cedula, $tipo_cedula,  $celular, $telefono, $Usuario_Fecha_Ingreso, $Usuario_Fecha_Cesantia, $Usuario_Fecha_Recontratacion, $Usuario_Nombre_Usuario, $Usuario_Observaciones, $Usuario_Password, $Usuario_Imagen_URL, $Usuario_Correo_Electronico, $Usuario_Rango, $Sucursal_Correspondiente)
	{
		//echo $creador_empresa;
		//date_default_timezone_set("America/Costa_Rica");
	    //$Current_datetime = date("y/m/d : H:i:s", now());
		$data = array(
                        'Usuario_Codigo'=>mysql_real_escape_string($codigo),
						'Usuario_Nombre'=>mysql_real_escape_string($nombre),                        
						'Usuario_Apellidos'=>mysql_real_escape_string($apellidos),                        
						'Usuario_Cedula'=>mysql_real_escape_string($cedula),                        
						'Usuario_Tipo_Cedula'=>mysql_real_escape_string($tipo_cedula),                        
						'Usuario_Celular'=>mysql_real_escape_string($celular),                        
						'Usuario_Telefono'=>mysql_real_escape_string($telefono),                        
						'Usuario_Fecha_Ingreso'=>mysql_real_escape_string($Usuario_Fecha_Ingreso),                        
						//'Usuario_Fecha_Cesantia'=>mysql_real_escape_string(NULL),                        
						//'Usuario_Fecha_Recontratacion'=>mysql_real_escape_string(NULL),                        
						'Usuario_Nombre_Usuario'=>mysql_real_escape_string($Usuario_Nombre_Usuario),                        
						'Usuario_Observaciones'=>mysql_real_escape_string($Usuario_Observaciones),                        
						'Usuario_Password'=>MD5($Usuario_Password),                        
						'Usuario_Imagen_URL'=>mysql_real_escape_string($Usuario_Imagen_URL),                        
						'Usuario_Correo_Electronico'=>mysql_real_escape_string($Usuario_Correo_Electronico),                        
						'Usuario_Rango'=>mysql_real_escape_string($Usuario_Rango),                        
						'TB_02_Sucursal_Codigo'=>mysql_real_escape_string($Sucursal_Correspondiente)                      
                    );
		try{
        	$this->db->insert('TB_01_Usuario',$data); 
    	}
		catch(Exception $e)
		{return false;}
		
		//Verificamos y retornamos si se guardo en ba
		return $this->existe_Usuario_Cedula($cedula);
	}

	function getCantidadUsuarios()
	{
		return $this->db->count_all('TB_01_Usuario');
	}
	function actualizar($id, $data)
	{
			$this->db->where('Usuario_Codigo', mysql_real_escape_string($id));
			$this->db->update('TB_01_Usuario' ,$data);
	}	

	function getUsuarios()
	{
		$this -> db -> select('*');
		$this -> db -> from('TB_01_Usuario');
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		  return $query->result();
		}
		else
		{
		  return false;
		}
	}

	function getUsuario_Cedula($cedula){
		$this -> db -> select('*');
		$this -> db -> from('TB_01_Usuario');
		$this -> db -> where('Usuario_Cedula', mysql_real_escape_string($cedula));
		$this -> db -> limit(1);

		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	}	
	
	function getUsuario_Codigo($id_usuario)
	{
		//echo $id_usuario;
		$this -> db -> select('*');
		$this -> db -> from('TB_01_Usuario');
		$this -> db -> where('Usuario_Codigo', mysql_real_escape_string($id_usuario));
		$this -> db -> limit(1);
		
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	}	
	
	function existeUsuario($codigo, $sucursal)
	{		
		$this -> db -> from('TB_01_Usuario');
		$this -> db -> where('Usuario_Codigo', mysql_real_escape_string($codigo));
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this -> db -> limit(1);
		
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function isActivated($id)
	{
		$this -> db -> select('Usuario_Fecha_Cesantia');
		$this -> db -> from('TB_01_Usuario');
		$this -> db -> where('Usuario_Codigo', mysql_real_escape_string($id));
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		$result = $query->result();
		
		foreach($result as $row)
        {
			if($row -> Usuario_Fecha_Cesantia == NULL)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	function isActivatedByUserName($username){
		$this -> db -> select('Usuario_Fecha_Cesantia');
		$this -> db -> from('TB_01_Usuario');
		$this -> db -> where('Usuario_Nombre_Usuario', mysql_real_escape_string($username));
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		$result = $query->result();
		
		foreach($result as $row)
        {
			if($row -> Usuario_Fecha_Cesantia == NULL)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	function obtener_Imagen_Usuario($cedula){
		$this -> db -> select('Usuario_Imagen_URL');
		$this -> db -> from('TB_01_Usuario');
		$this -> db -> where('Usuario_Cedula', mysql_real_escape_string($cedula));
		$this -> db -> limit(1);

		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		  return $query->result();
		}
		else
		{
		  return false;
		}
	}	

	function isAdministrador($username, $password)
	{
		include '/../controllers/get_session_data.php'; //Esto es para traer la informacion de la sesion
		$this -> db -> select('Usuario_Codigo');
		$this -> db -> from('TB_01_Usuario');
		$this -> db -> where('Usuario_Nombre_Usuario', mysql_real_escape_string($username));
		$this -> db -> where('Usuario_Password', mysql_real_escape_string($password));
		$this -> db -> where('Usuario_Rango', 'administra');
		$this -> db -> where('TB_02_Sucursal_Codigo', $data['Sucursal_Codigo']); //Que pertenezca a la surcusal de factura
		$this -> db -> limit(1);

		$query = $this -> db -> get();

		if($query -> num_rows() == 1)
		{
		 return $query->result();
		}
		else
		{
		 return false;
		}
	}
	
	function agregarPermiso($usuario, $sucursal, $area, $valor){
		$data = array(
                        'Permisos_Area'=>mysql_real_escape_string($area),
						'Permisos_Value'=>mysql_real_escape_string($valor),                        
						'TB_01_Usuario_Usuario_Codigo'=>mysql_real_escape_string($usuario),                        
						'TB_01_Usuario_TB_02_Sucursal_Codigo'=>mysql_real_escape_string($sucursal) 
                    );
		try{
        	$this->db->insert('TB_15_Permisos',$data); 
    	}
		catch(Exception $e)
		{return false;}
	}
	
	function eliminarPermisosUsuario($codigo_usuario, $sucursal){
		$this->db->where('TB_01_Usuario_Usuario_Codigo', $codigo_usuario);
		$this->db->where('TB_01_Usuario_TB_02_Sucursal_Codigo', $sucursal);
		$this->db->delete('TB_15_Permisos');
	}
	
	function getTransacciones(){
		return $this->db->get('TB_12_Transacciones')->result();
	}
	
}//FIN DE LA CLASE
?>
