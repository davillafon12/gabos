<?php
Class banco extends CI_Model
{
	function getBancos()
	{
		$this -> db -> select('*');
		$this -> db -> from('TB_22_Banco');
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
	
	function registrar($nombre, $comision, $usuario)
	{				
		$data = array(					
                        'Banco_Nombre'=>mysql_real_escape_string($nombre), 
						'Banco_Comision_Porcentaje'=>mysql_real_escape_string($comision),
						'Banco_Creado_Por'=>mysql_real_escape_string($usuario)
                    );
		try{
        $this->db->insert('TB_22_Banco',$data); }
		catch(Exception $e)
		{return false;}
		//Verificamos y retornamos si se guardo en base de datos
		//return $this->es_codigo_usado($id_empresa);
		return $this->db->insert_id();
	}
	
	function eliminar($id){
		$this->db->delete('TB_22_Banco', array('Banco_Codigo' => $id));
	}
	
	function getBanco($id)
	{
		$this -> db -> select('*');
		$this -> db -> from('TB_22_Banco');		
		$this -> db -> where('Banco_Codigo', mysql_real_escape_string($id));
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
	
	function actualizar($id, $data)
	{
		    
			$this->db->where('Banco_Codigo', mysql_real_escape_string($id));
			$this->db->update('TB_22_Banco' ,$data);
		
	}
	
	function getComision($id)
	{
		$this -> db -> select('Banco_Comision_Porcentaje');
		$this -> db -> from('TB_22_Banco');
		$this -> db -> where('Banco_Codigo', mysql_real_escape_string($id));
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		
		if($query->num_rows()==0)
		{return 0;} //NO COMISION
		else
		{
			$result = $query->result();
			foreach($result as $row)
			{	
				return $row->Banco_Comision_Porcentaje;
			}
		}
		
		
	}
	
} //FIN DE LA CLASE


?>