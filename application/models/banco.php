<?php 
Class banco extends CI_Model
{
	function getBancos()
	{
		$this -> db -> select('*');
		$this -> db -> from('tb_22_banco');
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
                        'Banco_Nombre'=>$nombre, 
						'Banco_Comision_Porcentaje'=>$comision,
						'Banco_Creado_Por'=>$usuario
                    );
		try{
        $this->db->insert('tb_22_banco',$data); }
		catch(Exception $e)
		{return false;}
		//Verificamos y retornamos si se guardo en base de datos
		//return $this->es_codigo_usado($id_empresa);
		return $this->db->insert_id();
	}
	
	function eliminar($id){
		$this->db->delete('tb_22_banco', array('Banco_Codigo' => $id));
	}
	
	function getBanco($id)
	{
		$this -> db -> select('*');
		$this -> db -> from('tb_22_banco');		
		$this -> db -> where('Banco_Codigo', $id);
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
		    
			$this->db->where('Banco_Codigo', $id);
			$this->db->update('tb_22_banco' ,$data);
		
	}
	
	function getComision($id)
	{
		$this -> db -> select('Banco_Comision_Porcentaje');
		$this -> db -> from('tb_22_banco');
		$this -> db -> where('Banco_Codigo', $id);
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