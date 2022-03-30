
<?php
Class User extends CI_Model
{


 function login($username, $password, $crypto = true)
 {
   $this -> db -> select('Usuario_Codigo, Usuario_Cedula, Usuario_Nombre_Usuario, Usuario_Password, TB_02_Sucursal_Codigo, Usuario_Imagen_URL, Usuario_Nombre, Usuario_Apellidos, Usuario_Observaciones, Usuario_Rango, Usuario_Fecha_Cesantia');
   $this -> db -> from('tb_01_usuario');
   $this -> db -> where('Usuario_Nombre_Usuario', $username);
   $this -> db -> where('Usuario_Password', $crypto ? MD5($password) : $password);
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
	$this -> db -> from('tb_01_usuario');
	$this -> db -> where('Usuario_Cedula', $cedula);
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

	function getUserById($id){
		$this -> db -> from('tb_01_usuario');
		$this -> db -> where('Usuario_Codigo', $id);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		return $query -> num_rows() > 0 ? $query->result()[0] : false;
	}

function existe_Nombre_Usuario($nombre){
	$this -> db -> select('Usuario_Nombre_Usuario');
	$this -> db -> from('tb_01_usuario');
	$this -> db -> where('Usuario_Nombre_Usuario', $nombre);
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
                'Trans_Descripcion' => $descripcion,
                'Trans_Fecha_Hora' => date("y/m/d : H:i:s", now()),
				'Trans_Tipo' => $tipo,
				'Trans_IP' => $this->input->ip_address(),
                'TB_01_Usuario_Usuario_Codigo' => $id_usuario,
                'TB_01_Usuario_TB_02_Sucursal_Codigo' => $id_sucursal,
                );
        $this->db->insert('tb_12_transacciones',$data);
    }

	function get_permisos($id_usuario, $id_sucursal)
	{
		$this -> db -> select('Permisos_Area, Permisos_Value');
		$this -> db -> from('tb_15_permisos');
		$this -> db -> where('TB_01_Usuario_Usuario_Codigo', $id_usuario);
		$this -> db -> where('TB_01_Usuario_TB_02_Sucursal_Codigo', $id_sucursal);
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
		$this -> db -> from('tb_01_usuario');
		$this -> db -> where('Usuario_Codigo', $id_usuario);
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
		$this -> db -> from('tb_01_usuario');
		$this -> db -> where('Usuario_Nombre_Usuario', $id_usuario);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
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
                        'Usuario_Codigo'=>$codigo,
						'Usuario_Nombre'=>$nombre,
						'Usuario_Apellidos'=>$apellidos,
						'Usuario_Cedula'=>$cedula,
						'Usuario_Tipo_Cedula'=>$tipo_cedula,
						'Usuario_Celular'=>$celular,
						'Usuario_Telefono'=>$telefono,
						'Usuario_Fecha_Ingreso'=>$Usuario_Fecha_Ingreso,
						//'Usuario_Fecha_Cesantia'=>NULL,
						//'Usuario_Fecha_Recontratacion'=>NULL,
						'Usuario_Nombre_Usuario'=>$Usuario_Nombre_Usuario,
						'Usuario_Observaciones'=>$Usuario_Observaciones,
						'Usuario_Password'=>md5($Usuario_Password),
						'Usuario_Imagen_URL'=>$Usuario_Imagen_URL,
						'Usuario_Correo_Electronico'=>$Usuario_Correo_Electronico,
						'Usuario_Rango'=>$Usuario_Rango,
						'TB_02_Sucursal_Codigo'=>$Sucursal_Correspondiente
                    );
		//print_r($data);
		try{
        	$this->db->insert('tb_01_usuario',$data);
    	}
		catch(Exception $e)
		{return false;}

		//Verificamos y retornamos si se guardo en ba
		return $this->existe_Usuario_Cedula($cedula);
		//return true;
	}

	function getCantidadUsuarios()
	{
            $resultado = 0;
            $this->db->select_max('Usuario_Codigo');
            $query =  $this->db->get('tb_01_usuario');
            foreach ($query->result() as $row) {
                    $resultado = $row->Usuario_Codigo;
            }
            return $resultado;
	}
	function actualizar($id, $data)
	{
			$this->db->where('Usuario_Codigo', $id);
			$this->db->update('tb_01_usuario' ,$data);
	}

	function getUsuarios()
	{
		$this -> db -> select('*');
		$this -> db -> from('tb_01_usuario');
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
		$this -> db -> from('tb_01_usuario');
		$this -> db -> where('Usuario_Cedula', $cedula);
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
		$this -> db -> from('tb_01_usuario');
		$this -> db -> where('Usuario_Codigo', $id_usuario);
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
		$this -> db -> from('tb_01_usuario');
		$this -> db -> where('Usuario_Codigo', $codigo);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
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
		$this -> db -> from('tb_01_usuario');
		$this -> db -> where('Usuario_Codigo', $id);
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
		$this -> db -> from('tb_01_usuario');
		$this -> db -> where('Usuario_Nombre_Usuario', $username);
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
		$this -> db -> from('tb_01_usuario');
		$this -> db -> where('Usuario_Cedula', $cedula);
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

	function isAdministrador($username, $password, $sucursal)
	{
		$this->db->where("Usuario_Nombre_Usuario", $username);
		$this->db->where("Usuario_Password", $password);
		$this->db->where("TB_02_Sucursal_Codigo", $sucursal);
		$this->db->where_in("Usuario_Rango", array("administra","avanzado"));
		$this->db->from("tb_01_usuario");

		$query = $this->db->get();

		return $query -> num_rows() == 0 ? false : true;
	}

	function isAdministradorPorCodigo($codigoUsuario)
	{
		$this -> db -> from('tb_01_usuario');
		$this -> db -> where('Usuario_Codigo', $codigoUsuario);
		$this -> db -> where_in('Usuario_Rango', array('administra','avanzado'));
		$query = $this -> db -> get();
		//var_dump($query->result());
		if($query -> num_rows() != 0)
		{
		 return 1;
		}
		else
		{
		 return 0;
		}
	}

	function agregarPermiso($usuario, $sucursal, $area, $valor){
		$data = array(
                        'Permisos_Area'=>$area,
						'Permisos_Value'=>$valor,
						'TB_01_Usuario_Usuario_Codigo'=>$usuario,
						'TB_01_Usuario_TB_02_Sucursal_Codigo'=>$sucursal
                    );
		try{
        	$this->db->insert('tb_15_permisos',$data);
    	}
		catch(Exception $e)
		{return false;}
	}

	function eliminarPermisosUsuario($codigo_usuario, $sucursal){
		$this->db->where('TB_01_Usuario_Usuario_Codigo', $codigo_usuario);
		$this->db->where('TB_01_Usuario_TB_02_Sucursal_Codigo', $sucursal);
		$this->db->delete('tb_15_permisos');
	}

	function getTransacciones(){
		return $this->db->get('tb_12_transacciones')->result();
	}

	function obtenerTransaccionesParaTabla($columnaOrden, $tipoOrden, $busqueda, $inicio, $cantidad, $sucursal){
		/*
			SELECT 	Trans_Codigo as codigo,
					Trans_Descripcion as descripcion,
					Trans_Fecha_Hora as fecha,
					Trans_Tipo as tipo,
					TB_01_Usuario_Usuario_Codigo as usuario_codigo,
					CONCAT(Usuario_Nombre,' ',Usuario_Apellidos) as usuario_nombre,
					Usuario_Nombre_Usuario as usuario_user
			FROM tb_12_transacciones
			JOIN tb_01_usuario ON tb_01_usuario.Usuario_Codigo = tb_12_transacciones.TB_01_Usuario_Usuario_Codigo
			WHERE (Trans_Codigo LIKE '%%' OR
				   Trans_Descripcion LIKE '%%' OR
				   Trans_Tipo LIKE '%%' OR
				   Usuario_Nombre LIKE '%%' OR
				   Usuario_Apellidos LIKE '%%' OR
				   Usuario_Nombre_Usuario LIKE '%%')
			AND TB_01_Usuario_TB_02_Sucursal_Codigo = 0
			ORDER BY Trans_Codigo DESC
			LIMIT 20,10
		*/
		return $this->db->query("
			SELECT 	Trans_Codigo as codigo,
					Trans_Descripcion as descripcion,
					Trans_Fecha_Hora as fecha,
					Trans_Tipo as tipo,
					Trans_IP as ip,
					TB_01_Usuario_Usuario_Codigo as usuario_codigo,
					CONCAT(Usuario_Nombre,' ',Usuario_Apellidos) as usuario_nombre,
					Usuario_Nombre_Usuario as usuario_user
			FROM tb_12_transacciones
			JOIN tb_01_usuario ON tb_01_usuario.Usuario_Codigo = tb_12_transacciones.TB_01_Usuario_Usuario_Codigo
			WHERE (Trans_Codigo LIKE '%$busqueda%' OR
				   Trans_Descripcion LIKE '%$busqueda%' OR
				   Trans_Tipo LIKE '%$busqueda%' OR
				   Trans_IP  LIKE '%$busqueda%' OR
				   Usuario_Nombre LIKE '%$busqueda%' OR
				   Usuario_Apellidos LIKE '%$busqueda%' OR
				   Usuario_Nombre_Usuario LIKE '%$busqueda%')
			AND TB_01_Usuario_TB_02_Sucursal_Codigo = $sucursal
			ORDER BY $columnaOrden $tipoOrden
			LIMIT $inicio,$cantidad
		");
	}

	function obtenerTransaccionesParaTablaFiltrados($columnaOrden, $tipoOrden, $busqueda, $inicio, $cantidad, $sucursal){
		/*
			SELECT 	Trans_Codigo as codigo,
					Trans_Descripcion as descripcion,
					Trans_Fecha_Hora as fecha,
					Trans_Tipo as tipo,
					TB_01_Usuario_Usuario_Codigo as usuario_codigo,
					CONCAT(Usuario_Nombre,' ',Usuario_Apellidos) as usuario_nombre,
					Usuario_Nombre_Usuario as usuario_user
			FROM tb_12_transacciones
			JOIN tb_01_usuario ON tb_01_usuario.Usuario_Codigo = tb_12_transacciones.TB_01_Usuario_Usuario_Codigo
			WHERE (Trans_Codigo LIKE '%%' OR
				   Trans_Descripcion LIKE '%%' OR
				   Trans_Tipo LIKE '%%' OR
				   Usuario_Nombre LIKE '%%' OR
				   Usuario_Apellidos LIKE '%%' OR
				   Usuario_Nombre_Usuario LIKE '%%')
			AND TB_01_Usuario_TB_02_Sucursal_Codigo = 0
		*/
		return $this->db->query("
			SELECT 	Trans_Codigo as codigo,
					Trans_Descripcion as descripcion,
					Trans_Fecha_Hora as fecha,
					Trans_Tipo as tipo,
					Trans_IP as ip,
					TB_01_Usuario_Usuario_Codigo as usuario_codigo,
					CONCAT(Usuario_Nombre,' ',Usuario_Apellidos) as usuario_nombre,
					Usuario_Nombre_Usuario as usuario_user
			FROM tb_12_transacciones
			JOIN tb_01_usuario ON tb_01_usuario.Usuario_Codigo = tb_12_transacciones.TB_01_Usuario_Usuario_Codigo
			WHERE (Trans_Codigo LIKE '%$busqueda%' OR
				   Trans_Descripcion LIKE '%$busqueda%' OR
				   Trans_Tipo LIKE '%$busqueda%' OR
				   Trans_IP  LIKE '%$busqueda%' OR
				   Usuario_Nombre LIKE '%$busqueda%' OR
				   Usuario_Apellidos LIKE '%$busqueda%' OR
				   Usuario_Nombre_Usuario LIKE '%$busqueda%')
			AND TB_01_Usuario_TB_02_Sucursal_Codigo = $sucursal
		");
	}

	function getTotalTransaccionesEnSucursal($sucursal){
		$this->db->where('TB_01_Usuario_TB_02_Sucursal_Codigo', $sucursal);
		$this->db->from('tb_12_transacciones');
		return $this->db->get()->num_rows();
	}

	function getVendedores($sucursal){
		/*
			SELECT Factura_Vendedor_Codigo
			FROM tb_07_factura
			WHERE TB_02_Sucursal_Codigo = 0
			AND Factura_Estado = 'cobrada'
			GROUP BY Factura_Vendedor_Codigo;
		*/
		$this->load->model("factura", "", true);

		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->factura->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->factura->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}
		$this->db->select('Factura_Vendedor_Codigo');
		$this->db->from('tb_07_factura');
		$this->db->where('tb_02_sucursal_codigo',$sucursal);
		$this->db->where('Factura_Estado','cobrada');
		$this->db->group_by('Factura_Vendedor_Codigo');
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result();
		}
	}

	// Registro Bitacora de Cliente


	function guardar_Bitacora_Cliente($Cliente_Cedula, $Sucursal, $Usuario, $Trans_Tipo, $Descripcion)
    {
	    date_default_timezone_set("America/Costa_Rica");
        $data = array(
				'Cliente_Cedula' => $Cliente_Cedula,
				'Sucursal' => $Sucursal,
				'Usuario' => $Usuario,
                'Trans_Fecha_Hora' => date("y/m/d : H:i:s", now()),
				'Trans_Tipo' => $Trans_Tipo,
				'Trans_IP' => $this->input->ip_address(),
                'Trans_Descripcion' => $Descripcion
                );
        $this->db->insert('tb_60_bitacora_cliente',$data);
	}

	function hasPermission($userID, $sucursal, $permissionKey){
		$this -> db -> from('tb_15_permisos');
		$this -> db -> where('TB_01_Usuario_Usuario_Codigo', $userID);
		$this -> db -> where('TB_01_Usuario_TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Permisos_Area', $permissionKey);

		$query = $this -> db -> get();

		$data = array(); // create a variable to hold the information

		return $query->num_rows() > 0;
    }

}//FIN DE LA CLASE
?>

