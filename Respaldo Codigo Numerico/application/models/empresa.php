<?php
Class empresa extends CI_Model
{
	function es_codigo_usado($Codigo_evaluar){
		$this -> db -> select('Codigo');
		$this -> db -> from('TB_02_sucursal');
		$this -> db -> where('Codigo', mysql_real_escape_string($Codigo_evaluar));
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
		
	function registrar($id_empresa, $nombre_empresa, $telefono_empresa, $observaciones_empresa, $direccion_empresa, $creador_empresa, $Empresa_Administrador, $leyenda_tributacion, $cedula, $fax, $email)
	{
		//echo $creador_empresa;
		date_default_timezone_set("America/Costa_Rica");
	    $Current_datetime = date("y/m/d : H:i:s", now());		
		$data = array(         
						'Codigo'=>mysql_real_escape_string($id_empresa),
						'Sucursal_Cedula'=>mysql_real_escape_string($cedula), 
                        'Sucursal_Nombre'=>mysql_real_escape_string($nombre_empresa), 
						'Sucursal_Telefono'=>mysql_real_escape_string($telefono_empresa),
						'Sucursal_Fax'=>mysql_real_escape_string($fax), 
						'Sucursal_Email'=>mysql_real_escape_string($email), 
						'Sucursal_Direccion'=>mysql_real_escape_string($direccion_empresa),
						'Sucursal_Fecha_Ingreso'=>$Current_datetime,
						'Sucursal_Observaciones'=>mysql_real_escape_string($observaciones_empresa),	
						'Sucursal_Creador'=>mysql_real_escape_string($creador_empresa),
						'Sucursal_Administrador'=>mysql_real_escape_string($Empresa_Administrador),
						'Sucursal_Estado'=> 1,
						'Sucursal_leyenda_tributacion'=> mysql_real_escape_string($leyenda_tributacion)
                    );
		try{
        $this->db->insert('tb_02_sucursal',$data); }
		catch(Exception $e)
		{return false;}
		//Verificamos y retornamos si se guardo en base de datos
		return $this->es_codigo_usado($id_empresa);
	}
	
	function getCantidadEmpresas()
	{
		return $this->db->count_all('tb_02_sucursal');
	}
	
	function getEmpresas()
	{
		$this -> db -> select('*');
		$this -> db -> from('TB_02_sucursal');
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
	
	function getEmpresa($id)
	{
		$this -> db -> select('*');
		$this -> db -> from('TB_02_sucursal');		
		$this -> db -> where('Codigo', mysql_real_escape_string($id));
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
	
	function getNombreEmpresa($id)
	{
		$this -> db -> select('Sucursal_Nombre');
		$this -> db -> from('TB_02_sucursal');		
		$this -> db -> where('Codigo', mysql_real_escape_string($id));
		$this -> db -> limit(1);
		
		$query = $this -> db -> get();
		$result = $query->result();
		
		foreach($result as $row)
        {
			return $row -> Sucursal_Nombre;			
		}
	}
	
	function isActivated($id)
	{
		$this -> db -> select('Sucursal_Fecha_Desactivacion');
		$this -> db -> from('TB_02_sucursal');
		$this -> db -> where('Codigo', mysql_real_escape_string($id));
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		$result = $query->result();
		
		foreach($result as $row)
        {
			if($row -> Sucursal_Fecha_Desactivacion == NULL)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	function actualizar($id, $data)
	{
		    
			$this->db->where('codigo', mysql_real_escape_string($id));
			$this->db->update('TB_02_sucursal' ,$data);
		
	}
	
	function get_empresas_ids_array()
	{
		$this -> db -> select('codigo, Sucursal_Nombre');
		$this -> db -> from('TB_02_Sucursal');
		$data = array(); // create a variable to hold the information			
		
		$query = $this -> db -> get();
		
			$result = $query->result();
			foreach($result as $row)
			{			
			   $data[$row->Sucursal_Nombre] = $row->codigo;  // add the row in to the results (data) array
			}
	   
	   return $data;  
    }
}


?>