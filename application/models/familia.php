<?php
Class familia extends CI_Model
{
	function es_codigo_usado($codigo, $sucursal){
		$this -> db -> from('TB_05_Familia');
		$this -> db -> where('Familia_Codigo', $codigo);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
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
	
	function existeFamilia($familia, $sucursal){
		$this -> db -> select('Familia_Codigo');
		$this -> db -> from('TB_05_Familia');
		$this -> db -> where('Familia_Codigo', $familia);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
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
	
	function getCantidadFamilias($sucursal)
	{
		//Este metodo lo que hace realemente es devolver el siguiente codigo para la nueva familia
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		return $this->db->count_all_results('TB_05_Familia');
	}
	
	function registrar($id_familia, $nombre_familia, $observaciones_familia, $sucursal_familia, $nombre)
	{
		//echo $creador_empresa;
		//echo "PASO POR AQUI";
		date_default_timezone_set("America/Costa_Rica");
	    $Current_datetime = date("y/m/d : H:i:s", now());
		$data = array(
                        'Familia_Codigo'=>mysql_real_escape_string($id_familia),
                        'Familia_Nombre'=>mysql_real_escape_string($nombre_familia), 
						//'Familia_Descuento'=>mysql_real_escape_string($descuento_familia),						
						'Familia_Fecha_Creacion'=>$Current_datetime,
						'Familia_Observaciones'=>mysql_real_escape_string($observaciones_familia),	
						'TB_02_Sucursal_Codigo'=>mysql_real_escape_string($sucursal_familia),
						'Familia_Creador'=>mysql_real_escape_string($nombre),
						'Familia_Estado'=> 1
                    );
		try{
        $this->db->insert('TB_05_Familia',$data); }
		catch(Exception $e)
		{return false;}
		
		//Verificamos y retornamos si se guardo en base de datos
		return $this->es_codigo_usado($id_familia, $sucursal_familia);
	}
	
	function getFamilias($sucursal)
	{
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> from('TB_05_familia');		
		$query = $this -> db -> get();
       
		return $query->result();
	}
	
	function getFamiliasTodas()
	{
		//$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> from('TB_05_familia');		
		$query = $this -> db -> get();
       
		return $query->result();
	}
	
	function getFamilia($id, $sucursal)
	{
		$this -> db -> from('TB_05_familia');		
		$this -> db -> where('Familia_Codigo', mysql_real_escape_string($id));
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
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
	
	function getNombreFamilia($id)
	{
		$this -> db -> select('Familia_Nombre');
		$this -> db -> from('TB_05_Familia');		
		$this -> db -> where('Familia_Codigo', mysql_real_escape_string($id));
		$this -> db -> limit(1);
		
		$query = $this -> db -> get();
		$result = $query->result();
		
		foreach($result as $row)
        {
			return $row -> Familia_Nombre;			
		}
	}
	
	function getNombreFamiliaSucursal($id, $sucursal)
	{
		$this -> db -> select('Familia_Nombre');
		$this -> db -> from('TB_05_Familia');		
		$this -> db -> where('Familia_Codigo', mysql_real_escape_string($id));
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this -> db -> limit(1);
		
		$query = $this -> db -> get();
		$result = $query->result();
		
		if($query -> num_rows() != 0)
		{
			foreach($result as $row)
			{
				return $row -> Familia_Nombre;			
			}
		}else{return false;}
	}
	
	function isActivated($codigo, $sucursal)
	{
		$this -> db -> select('Familia_Fecha_Desactivacion');
		$this -> db -> from('TB_05_Familia');
		$this -> db -> where('Familia_Codigo', $codigo);
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		$result = $query->result();
		
		foreach($result as $row)
        {
			if($row -> Familia_Fecha_Desactivacion == NULL)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	function actualizar($codigo, $sucursal, $data)
	{		    
		$this->db->where('Familia_Codigo', mysql_real_escape_string($codigo));
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this->db->update('TB_05_Familia' ,$data);		
	}
	
	function get_familias_ids_array($sucursal)
	{
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> from('TB_05_Familia');
		$data = array(); // create a variable to hold the information	
		
		$query = $this -> db -> get();
		
			$result = $query->result();
			foreach($result as $row)
			{			
			   $data[$row->Familia_Nombre] = $row->Familia_Codigo;  // add the row in to the results (data) array
			}
	   
	   return $data;  
    }	
	
	
}


?>