<?php
Class empresa extends CI_Model
{
	function es_codigo_usado($Codigo_evaluar){
		$this -> db -> select('Codigo');
		$this -> db -> from('tb_02_sucursal');
		$this -> db -> where('Codigo', $Codigo_evaluar);
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

	function registrar( $id_empresa,
                            $nombre_empresa,
                            $telefono_empresa,
                            $observaciones_empresa,
                            $direccion_empresa,
                            $creador_empresa,
                            $Empresa_Administrador,
                            $leyenda_tributacion,
                            $cedula,
                            $fax,
                            $email,
                            $userT,
                            $passT,
                            $ambiT,
                            $pinT,
                            $tipo_identificacion,
                            $cod_telefono_empresa,
                            $cod_fax_empresa,
                            $provincia,
                            $canton,
                            $distrito,
                            $barrio,
							$codigo_actividad,
							$requiereFE)
	{
		//echo $creador_empresa;
		date_default_timezone_set("America/Costa_Rica");
	    $Current_datetime = date(DB_DATETIME_FORMAT, now());
		$data = array(
                                'Codigo'=>$id_empresa,
                                'Sucursal_Cedula'=>$cedula,
                                'Sucursal_Nombre'=>$nombre_empresa,
                                'Sucursal_Telefono'=>$telefono_empresa,
                                'Sucursal_Fax'=>$fax,
                                'Sucursal_Email'=>$email,
                                'Sucursal_Direccion'=>$direccion_empresa,
                                'Sucursal_Fecha_Ingreso'=>$Current_datetime,
                                'Sucursal_Observaciones'=>$observaciones_empresa,
                                'Sucursal_Creador'=>$creador_empresa,
                                'Sucursal_Administrador'=>$Empresa_Administrador,
                                'Sucursal_Estado'=> 1,
                                'Sucursal_leyenda_tributacion'=> $leyenda_tributacion,
                                'Usuario_Tributa'=> $userT,
                                'Pass_Tributa'=> $passT,
                                'Ambiente_Tributa'=> $ambiT,
                                'Pass_Certificado_Tributa'=> $pinT,
                                'Tipo_Cedula'=> $tipo_identificacion,
                                'Codigo_Pais_Telefono'=> $cod_telefono_empresa,
                                'Codigo_Pais_Fax'=> $cod_fax_empresa,
                                'Provincia'=> $provincia,
                                'Canton'=> $canton,
                                'Distrito'=> $distrito,
                                'Barrio'=> $barrio,
								'CodigoActividad'=> $codigo_actividad,
								'RequiereFE' => $requiereFE
                    );
		try{
        $this->db->insert('tb_02_sucursal',$data); }
		catch(Exception $e)
		{return false;}
		//Verificamos y retornamos si se guardo en base de datos
		return $this->es_codigo_usado($id_empresa);
	}

	function getCantidadEmpresas(){
		$this -> db -> select('MAX(Codigo)+1 as Siguiente', false);
		$this -> db -> from('tb_02_sucursal');
		$query = $this -> db -> get();

		if($query -> num_rows() != 0){
		  return $query->result()[0]->Siguiente;
		}else{
		  die("Error creating new record.");
		}
	}

	function getEmpresas()
	{
		$this -> db -> select('*');
		$this -> db -> from('tb_02_sucursal');
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
		$this -> db -> from('tb_02_sucursal');
		$this -> db -> where('Codigo', $id);
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

	function getLeyendaEmpresa($id)
	{
		$this -> db -> select('Sucursal_leyenda_tributacion');
		$this -> db -> from('tb_02_sucursal');
		$this -> db -> where('Codigo', $id);
		$this -> db -> limit(1);

		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		  return $query->result()[0]->Sucursal_leyenda_tributacion;
		}
		else
		{
		  return false;
		}
	}

	function getEmpresaImpresion($id)
	{
		$this -> db -> select('Sucursal_Cedula AS cedula, Sucursal_Nombre AS nombre, Sucursal_Telefono AS telefono, Sucursal_Email AS email, Sucursal_leyenda_tributacion AS leyenda, Sucursal_Administrador as administrador, Sucursal_Direccion as direccion, RequiereFE as isFE');
		$this -> db -> from('tb_02_sucursal');
		$this -> db -> where('Codigo', $id);
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
		if($id==-1){return "N/A";}
		$this -> db -> select('Sucursal_Nombre');
		$this -> db -> from('tb_02_sucursal');
		$this -> db -> where('Codigo', $id);
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
		$this -> db -> from('tb_02_sucursal');
		$this -> db -> where('Codigo', $id);
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

	function actualizar($id, $data){
            $this->db->where('codigo', $id);
            $this->db->update('tb_02_sucursal' ,$data);
	}

	function get_empresas_ids_array()
	{
		$this -> db -> select('codigo, Sucursal_Nombre');
		$this -> db -> from('tb_02_sucursal');
		$data = array(); // create a variable to hold the information

		$query = $this -> db -> get();

			$result = $query->result();
			foreach($result as $row)
			{
			   $data[$row->Sucursal_Nombre] = $row->codigo;  // add the row in to the results (data) array
			}

	   return $data;
    }

    function getClienteLigaByEmpresa($empresa){
    		$this->db->where("Sucursal", $empresa);
    		$this->db->from("tb_48_relacion_sucursal_cliente");
    		$query = $this->db->get();
    		if($query->num_rows() == 0){
    				return false;
    		}else{
    				$this->load->model("cliente", "", true);
    				$result = $query->result()[0];
    				//Traemos la info del cliente
    				$result->informacion = $this->cliente->getNombreCliente($result->Cliente);
    				return $result;
    		}
    }

    function registrarClienteConEmpresaLiga($empresa, $cliente){
    		$datos = array("Cliente"=>$cliente, "Sucursal"=>$empresa);
    		$this->db->insert("tb_48_relacion_sucursal_cliente", $datos);
    }

    function actualizarLigaEmpresaCliente($empresa, $cliente){
    		//Primero eliminamos cualquier relacion
    		$this->eliminarLigaConCliente($empresa);

    		//Verificamos si existe el cliente
    		$this->load->model("cliente", "", true);
    		if($this->cliente->existe_Cliente($cliente)){
    				$this->registrarClienteConEmpresaLiga($empresa, $cliente);
    		}
    }

    function eliminarLigaConCliente($empresa){
    		$this->db->where("Sucursal", $empresa);
    		$this->db->delete("tb_48_relacion_sucursal_cliente");
	}

	function empresaUsaCabys($codigo){
		$this -> db -> select('RequiereFE');
		$this -> db -> from('tb_02_sucursal');
		$this -> db -> where('Codigo', $codigo);
		$query = $this->db->get();
		if($query->num_rows() == 0){
			return true; //Defecto
		}else{
			return $query->result()[0]->RequiereFE == 1;
		}
	}
}


?>